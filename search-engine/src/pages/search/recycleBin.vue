<template>
  <q-page class="q-pa-md">
    <q-toolbar class="q-mb-md">
      <q-space />
      <q-btn
        dense
        borderless
        flat
        icon="arrow_back"
        label="BACK"
        @click="$router.go(-1)"
      />
    </q-toolbar>

    <div><h5>Recycle Bin</h5></div>

    <div class="justify-start">
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
              icon="restore"
              @click.stop="restoreRow(props.row)"
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
  </q-page>
</template>

<script>
import { api } from "boot/axios";

export default {
  data() {
    return {
      rows: [],
      columns: [
        { name: "id", label: "Ref No.", field: "id", align: "center" },
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
      ]
    };
  },

  methods: {
    fetchData() {
      const token = localStorage.getItem("token");
      api
        .get("/fetch_recycleBin.php", {
          headers: {
            Authorization: `Bearer ${token}`
          }
        })
        .then((response) => {
          if (response.data.status === "success") {
            this.rows = response.data.data;
          } else {
            this.$q.notify({
              type: "negative",
              message: "Failed to load recycle bin data.",
              position: "bottom-right",
              style: { fontSize: "18px", padding: "20px" }
            });
          }
        })
        .catch((error) => {
          console.error("Error fetching recycle bin data:", error);
          this.$q.notify({
            type: "negative",
            message: "Failed to fetch recycle bin data.",
            position: "bottom-right",
            style: { fontSize: "18px", padding: "20px" }
          });
          if (error.response && error.response.status === 401) {
            this.$router.push({ name: "loginPage" });
          }
        });
    },

    restoreRow(row) {
      const token = localStorage.getItem("token");
      this.$q
        .dialog({
          title: "Confirm",
          message: "Are you sure you want to restore this item?",
          ok: {
            label: "Yes",
            color: "positive"
          },
          cancel: true
        })
        .onOk(() => {
          api
            .put(
              "/restore_row.php",
              { id: row.id },
              { headers: { Authorization: `Bearer ${token}` } }
            )
            .then((response) => {
              if (response.data.status === "success") {
                this.fetchData();
                this.$q.notify({
                  type: "positive",
                  message: "Item restored successfully!",
                  position: "bottom-right",
                  style: { fontSize: "18px", padding: "20px" }
                });
              } else {
                this.$q.notify({
                  type: "negative",
                  message: response.data.message || "Failed to restore item.",
                  position: "bottom-right",
                  style: { fontSize: "18px", padding: "20px" }
                });
              }
            })
            .catch((error) => {
              console.error("Error restoring item:", error);
              this.$q.notify({
                type: "negative",
                message: "Failed to restore item.",
                position: "bottom-right",
                style: { fontSize: "18px", padding: "20px" }
              });
            });
        });
    },

    deleteRow(row) {
      const token = localStorage.getItem("token");
      this.$q
        .dialog({
          title: "Confirm",
          message: "Are you sure you want to permanently delete this item?",
          ok: {
            label: "Yes",
            color: "negative"
          },
          cancel: true
        })
        .onOk(() => {
          api
            .delete(`/perma_delete.php?id=${row.id}`, {
              headers: { Authorization: `Bearer ${token}` }
            })
            .then((response) => {
              if (response.data.status === "success") {
                this.fetchData();
                this.$q.notify({
                  type: "positive",
                  message: "Item permanently deleted successfully!",
                  position: "bottom-right",
                  style: { fontSize: "18px", padding: "20px" }
                });
              } else {
                this.$q.notify({
                  type: "negative",
                  message: response.data.message || "Failed to delete item.",
                  position: "bottom-right",
                  style: { fontSize: "18px", padding: "20px" }
                });
              }
            })
            .catch((error) => {
              console.error("Error deleting item:", error);
              this.$q.notify({
                type: "negative",
                message: "Failed to delete item.",
                position: "bottom-right",
                style: { fontSize: "18px", padding: "20px" }
              });
            });
        });
    },

    goToDetails(evt, row) {
      // Placeholder for row-click navigation (e.g., to DetailsPage)
      console.log("Row clicked:", row.id);
    }
  },

  mounted() {
    const token = localStorage.getItem("token");
    if (!token) {
      this.$router.push({ name: "loginPage" });
    } else {
      this.fetchData();
    }
  }
};
</script>

<style lang="scss">
@import "../../layouts/styles/styles.scss";
</style>