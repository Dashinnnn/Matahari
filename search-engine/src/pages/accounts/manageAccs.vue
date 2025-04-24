<template>
  <q-page>
    <q-toolbar class="q-mb-md q-ml-md q-mr-md justify-between">
      <div><h5>Manage Accounts</h5></div>
      <q-space />
      <div>
        <q-btn
          dense
          borderless
          flat
          label="Back"
          icon="arrow_back"
          @click="$router.go(-1)"
          class="q-mr-xl"
        />
      </div>
    </q-toolbar>
    <div class="q-ml-lg q-mr-lg">
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
      </div>

      <div class="justify-start">
        <q-table
          :rows="rows"
          :columns="columns"
          row-key="id"
          :rows-per-page-options="[15]"
          @row-click="goToDetails"
          class="custom-header"
        >
          <template v-slot:body-cell-level="props">
            <q-td :props="props">
              <q-select
                v-model="props.row.level"
                :options="levelOptions"
                dense
                borderless
                @update:model-value="updateLevel(props.row)"
                style="width: 100px"
              />
            </q-td>
          </template>
          <template v-slot:body-cell-actions="props">
            <q-td :props="props">
              <q-btn
                :label="props.row.verified === 'YES' ? 'Un-Verify' : 'Verify'"
                @click.stop="toggleVerification(props.row)"
                flat
                dense
                class="q-pl-lg q-pr-lg"
              />
              <q-btn
                color="negative"
                icon="delete"
                @click.stop="deleteRow(props.row)"
                flat
                dense
                class="q-pl-lg q-pr-lg"
              />
            </q-td>
          </template>
        </q-table>
      </div>
    </div>
  </q-page>
</template>

<script>
import { api } from "../../boot/axios";

export default {
  data() {
    return {
      searchQuery: "",
      rows: [],
      levelOptions: ["ADMIN", "CLIENT", "UNKNOWN"],
      columns: [
        {
          name: "fname",
          label: "Full Name",
          field: (row) => `${row.fname} ${row.lname}`,
          align: "center",
        },
        {
          name: "username",
          label: "UserName",
          field: "username",
          align: "center",
        },
        {
          name: "Verified",
          label: "Verified",
          field: "verified",
          align: "center",
        },
        {
          name: "level",
          label: "Level",
          field: "level",
          align: "center",
        },
        {
          name: "actions",
          label: "Actions",
          align: "center",
          field: "actions",
        },
      ],
    };
  },

  methods: {
    fetchData() {
      const token = localStorage.getItem("token");
      const query = this.searchQuery ? `?q=${this.searchQuery}` : "";

      api
        .get(`http://localhost/searchEngine/manageAcc.php${query}`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        })
        .then((response) => {
          if (response.data.status === "success") {
            this.rows = response.data.data;
          } else {
            console.error("Unexpected response format:", response.data);
            this.rows = [];
          }
        })
        .catch((error) => {
          console.error("Error fetching data:", error);
          if (error.response && error.response.status === 401) {
            this.$router.push({ name: "loginPage" });
          }
          this.rows = [];
        });
    },

    deleteRow(row) {
      const token = localStorage.getItem("token");
      this.$q
        .dialog({
          title: "Confirm",
          message: "Are you sure you want to delete this user?",
          ok: { label: "Yes", color: "negative" },
          cancel: true,
        })
        .onOk(() => {
          api
            .delete(
              `http://localhost/searchEngine/deleteAcc.php?id=${row.id}`,
              {
                headers: { Authorization: `Bearer ${token}` },
              }
            )
            .then(() => {
              this.fetchData();
              this.$q.notify({
                color: "positive",
                message: "User deleted successfully!",
                position: "bottom-right",
                style: { fontSize: "18px", padding: "20px" },
              });
            })
            .catch((error) => {
              console.error("Delete error:", error);
              this.$q.notify({
                color: "negative",
                message: "Failed to delete user!",
                position: "bottom-right",
                style: { fontSize: "18px", padding: "20px" },
              });
            });
        });
    },

    toggleVerification(row) {
      const token = localStorage.getItem("token");
      const newStatus = row.verified === "YES" ? "NO" : "YES";
      const message = newStatus === "YES" ? "verify" : "un-verify";

      this.$q
        .dialog({
          title: "Confirm",
          message: `Are you sure you want to ${message} this user?`,
          ok: {
            label: "Yes",
            color: newStatus === "YES" ? "positive" : "negative",
          },
          cancel: true,
        })
        .onOk(() => {
          api
            .put(
              `http://localhost/searchEngine/verifyAcc.php`,
              { id: row.id, verified: newStatus },
              { headers: { Authorization: `Bearer ${token}` } }
            )
            .then(() => {
              this.fetchData();
              this.$q.notify({
                color: "positive",
                message: `User ${message} successfully!`,
                position: "bottom-right",
                style: { fontSize: "18px", padding: "20px" },
              });
            })
            .catch((error) => {
              console.error("Verification error:", error);
              this.$q.notify({
                color: "negative",
                message: `Failed to ${message} user!`,
                position: "bottom-right",
                style: { fontSize: "18px", padding: "20px" },
              });
            });
        });
    },

    updateLevel(row) {
      const token = localStorage.getItem("token");
      const newLevel = row.level;
      const message = newLevel.toLowerCase();

      this.$q
        .dialog({
          title: "Confirm",
          message: `Are you sure you want to set this user as ${message}?`,
          ok: { label: "Yes", color: "positive" },
          cancel: true,
        })
        .onOk(() => {
          api
            .put(
              `http://localhost/searchEngine/levelSet.php`,
              { id: row.id, level: newLevel },
              { headers: { Authorization: `Bearer ${token}` } }
            )
            .then(() => {
              this.fetchData();
              this.$q.notify({
                color: "positive",
                message: `User level set to ${message} successfully!`,
                position: "bottom-right",
                style: { fontSize: "18px", padding: "20px" },
              });
            })
            .catch((error) => {
              console.error("Level update error:", error);
              this.$q.notify({
                color: "negative",
                message: `Failed to update user level!`,
                position: "bottom-right",
                style: { fontSize: "18px", padding: "20px" },
              });
            });
        })
        .onCancel(() => {
          this.fetchData();
        });
    },
  },

  mounted() {
    const token = localStorage.getItem("token");
    if (!token) {
      this.$router.push({ name: "loginPage" });
    } else {
      this.fetchData();
    }
  },
};
</script>
