<template>
  <q-page class="q-pa-md">
    <q-toolbar class="q-mb-md">
      <div><h5>Search Engine</h5></div>
      <q-space />
      <q-btn-dropdown
        color="primary"
        flat
        dense
        v-if="
          userRole === 'ADMIN' ||
          userRole === 'SUPERADMIN' ||
          userRole === 'USER'
        "
      >
        <template v-slot:label>
          <div class="row items-center no-wrap">
            <font-awesome-icon :icon="['fas', 'gear']" class="q-mr-sm" />
            <span>Menu</span>
          </div>
        </template>
        <q-list>
          <!-- Recycle Bin (ADMIN/SUPERADMIN only) -->
          <q-item
            clickable
            v-close-popup
            @click="goTorecycleBin"
            v-if="userRole === 'ADMIN' || userRole === 'SUPERADMIN'"
          >
            <q-item-section>
              <q-item-label>Recycle Bin</q-item-label>
            </q-item-section>
          </q-item>
          <!-- Manage Accounts (ADMIN/SUPERADMIN only) -->
          <q-item
            clickable
            v-close-popup
            @click="goToManage"
            v-if="userRole === 'ADMIN' || userRole === 'SUPERADMIN'"
          >
            <q-item-section>
              <q-item-label>Manage Accounts</q-item-label>
            </q-item-section>
          </q-item>
          <!-- Download Excel (All roles) -->
          <q-item clickable v-close-popup @click="downloadExcel">
            <q-item-section>
              <q-item-label>Download Excel</q-item-label>
            </q-item-section>
          </q-item>
          <!-- Logout (All roles) -->
          <q-item clickable v-close-popup @click="logout">
            <q-item-section>
              <q-item-label>Logout</q-item-label>
            </q-item-section>
          </q-item>
        </q-list>
      </q-btn-dropdown>
    </q-toolbar>
    <div class="q-gutter-sm row items-center justify-between">
      <q-input
        v-model="searchQuery"
        placeholder="Search..."
        @keyup="fetchData"
        borderless
        hide-bottom-space
        dense
        class="searchBox"
      />
      <div>
        <!-- Only keep Add Data button here -->
        <q-btn
          dense
          flat
          label="Add Data"
          class="addBtn"
          @click="gotoAddPage"
          v-if="userRole === 'ADMIN' || userRole === 'SUPERADMIN'"
        />
      </div>
    </div>

    <div class="justify-start q-mt-lg">
      <q-table
        :rows="rows"
        :columns="columns"
        row-key="id"
        :rows-per-page-options="[15]"
        @row-click="goToDetails"
        class="custom-header"
      >
        <template v-slot:body-cell-actions="props">
          <q-td :props="props">
            <q-btn
              color="primary"
              icon="edit"
              @click.stop="editRow(props)"
              flat
              dense
              class="q-pl-lg q-pr-lg"
              v-if="userRole === 'ADMIN' || userRole === 'SUPERADMIN'"
            />
            <q-btn
              color="negative"
              icon="delete"
              @click.stop="deleteRow(props.row)"
              flat
              dense
              class="q-pl-lg q-pr-lg"
              v-if="userRole === 'ADMIN' || userRole === 'SUPERADMIN'"
            />
          </q-td>
        </template>
      </q-table>
    </div>
  </q-page>
</template>

<script>
import { api } from "boot/axios";
import _ from "lodash";

export default {
  data() {
    return {
      searchQuery: "",
      rows: [],
      columns: [
        { name: "name", label: "Name", field: "name", align: "center" },
        {
          name: "barangay",
          label: "Barangay",
          field: "barangay",
          align: "center"
        },
        {
          name: "designation",
          label: "Designation",
          field: "designation",
          align: "center"
        },
        {
          name: "actions",
          label: "Actions",
          align: "center",
          field: "actions"
        }
      ],
      userRole: ""
    };
  },

  methods: {
    fetchData: _.debounce(function () {
      const query = this.searchQuery ? `?q=${this.searchQuery}` : "";
      console.log("Fetching data with query:", query);

      api
        .get(`/api.php${query}`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`
          }
        })
        .then((response) => {
          if (
            response.data.status === "success" &&
            Array.isArray(response.data.data)
          ) {
            this.rows = response.data.data;
          } else {
            console.error("Unexpected data format:", response.data);
            this.rows = [];
            this.$q.notify({
              type: "negative",
              message: "No data found.",
              position: "bottom-right",
              style: { fontSize: "18px", padding: "20px" }
            });
          }
        })
        .catch((error) => {
          console.error("Error fetching data:", error);
          this.$q.notify({
            type: "negative",
            message: "Failed to fetch data. Please try again.",
            position: "bottom-right",
            style: { fontSize: "18px", padding: "20px" }
          });
          if (error.response?.status === 401) {
            this.$router.push({ name: "loginPage" });
          }
        });
    }, 300),

    goToDetails(evt, row) {
      if (row && row.id) {
        this.$router.push(`/details/${row.id}`);
      } else {
        console.error("ID not found in row:", row);
      }
    },

    gotoAddPage() {
      this.$router.push({ name: "addPage" });
    },

    goTorecycleBin() {
      this.$router.push({ name: "recycleBin" });
    },

    goToManage() {
      this.$router.push({ name: "manageAccs" });
    },

    editRow(props) {
      const row = props.row;
      if (row && row.id) {
        this.$router.push({ name: "editPage", params: { id: row.id } });
      } else {
        console.error("Row ID is missing:", row);
      }
    },

    deleteRow(row) {
      this.$q
        .dialog({
          title: "Confirm",
          message: "Are you sure you want to move this item to the recycle bin?",
          ok: { label: "Yes", color: "negative" },
          cancel: true
        })
        .onOk(() => {
          api
            .put(
              "/softDelete.php",
              { id: row.id },
              {
                headers: {
                  Authorization: `Bearer ${localStorage.getItem("token")}`
                }
              }
            )
            .then((response) => {
              if (response.data.status === "success") {
                this.fetchData();
                this.$q.notify({
                  type: "positive",
                  message: "Item moved to recycle bin successfully!",
                  position: "bottom-right",
                  style: { fontSize: "18px", padding: "20px" }
                });
              } else {
                this.$q.notify({
                  type: "negative",
                  message: response.data.message || "Failed to move item.",
                  position: "bottom-right",
                  style: { fontSize: "18px", padding: "20px" }
                });
              }
            })
            .catch((error) => {
              console.error("Error moving item:", error);
              this.$q.notify({
                type: "negative",
                message: "Failed to move item.",
                position: "bottom-right",
                style: { fontSize: "18px", padding: "20px" }
              });
            });
        });
    },

    logout() {
      localStorage.removeItem("token");
      localStorage.removeItem("role");
      this.$router.push({ name: "loginPage" });
      this.$q.notify({
        type: "info",
        message: "Logged out successfully.",
        position: "bottom-right",
        style: { fontSize: "18px", padding: "20px" }
      });
    },

    downloadExcel() {
      window.open(`${process.env.VUE_APP_API_URL}/exportData.php`, "_blank");
    }
  },

  mounted() {
    const token = localStorage.getItem("token");
    const userRole = localStorage.getItem("role")?.trim();

    if (token && userRole) {
      this.userRole = userRole;
    } else {
      console.warn("Role not found, defaulting to UNKNOWN.");
      this.userRole = "UNKNOWN";
    }

    console.log(
      "Checking role visibility:",
      this.userRole,
      this.userRole === "ADMIN" || this.userRole === "SUPERADMIN"
    );

    if (!token) {
      this.$router.push({ name: "loginPage" });
      this.$q.notify({
        type: "warning",
        message: "Please log in to access this page.",
        position: "bottom-right",
        style: { fontSize: "18px", padding: "20px" }
      });
      return;
    }

    this.fetchData();
  }
};
</script>

<style lang="scss">
@import "../../layouts/styles/index.scss";
</style>