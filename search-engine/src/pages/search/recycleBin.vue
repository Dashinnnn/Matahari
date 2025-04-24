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
import { api } from "../../boot/axios";

export default {
  data() {
    return {
      rows: [],
      columns: [
        { name: "id", label: "Ref No.", field: "id", align: "center" }, // Fixed typo: "lable" -> "label"
        { name: "name", label: "Name", field: "name", align: "center" },
        {
          name: "barangay",
          label: "Barangay",
          field: "barangay",
          align: "center",
        },
        {
          name: "designation",
          label: "Designation",
          field: "designation",
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
    fetchRecycledBinData() {
      const token = localStorage.getItem("token");
      api
        .get("http://localhost/searchEngine/fetch_recycleBin.php", {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        })
        .then((response) => {
          if (response.data.status === "success") {
            this.rows = response.data.data;
          } else {
            this.$q.notify({
              type: "negative",
              message: "Failed to load data",
              position: "bottom-right",
            });
          }
        })
        .catch((error) => {
          console.error("Error fetching recycle bin data:", error);
          if (error.response && error.response.status === 401) {
            this.$router.push({ name: "loginPage" });
          }
          this.$q.notify({
            type: "negative",
            message: "Error fetching data",
            position: "bottom-right",
          });
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
            color: "positive",
          },
          cancel: true,
        })
        .onOk(() => {
          api
            .put(
              "http://localhost/searchEngine/restore_row.php",
              { id: row.id },
              { headers: { Authorization: `Bearer ${token}` } }
            )
            .then((response) => {
              if (response.data.status === "success") {
                this.fetchRecycledBinData(); // Fixed method name
                this.$q.notify({
                  color: "positive",
                  message: "Item successfully restored!",
                  position: "bottom-right",
                });
              } else {
                this.$q.notify({
                  color: "negative",
                  message: response.data.message || "Failed to restore data!",
                  position: "bottom-right",
                });
              }
            })
            .catch((error) => {
              console.error("Error restoring row:", error);
              this.$q.notify({
                color: "negative",
                message: "Failed to restore data!",
                position: "bottom-right",
              });
            });
        });
    },

    deleteRow(row) {
      const token = localStorage.getItem("token");
      this.$q
        .dialog({
          title: "Confirm",
          message: "Are you sure you want to permanently delete this row?",
          ok: {
            label: "Yes",
            color: "negative",
          },
          cancel: true,
        })
        .onOk(() => {
          api
            .delete(
              `http://localhost/searchEngine/perma_delete.php?id=${row.id}`,
              { headers: { Authorization: `Bearer ${token}` } }
            )
            .then((response) => {
              if (response.data.status === "success") {
                this.fetchRecycledBinData(); // Fixed method name
                this.$q.notify({
                  color: "positive",
                  message: "Row deleted successfully!",
                  position: "bottom-right",
                });
              } else {
                this.$q.notify({
                  color: "negative",
                  message: response.data.message || "Failed to delete data!",
                  position: "bottom-right",
                });
              }
            })
            .catch((error) => {
              console.error("Error deleting row:", error);
              this.$q.notify({
                color: "negative",
                message: "Failed to delete data!",
                position: "bottom-right",
              });
            });
        });
    },
  },

  mounted() {
    const token = localStorage.getItem("token");
    if (!token) {
      this.$router.push({ name: "loginPage" });
    } else {
      this.fetchRecycledBinData();
    }
  },
};
</script>

<style lang="scss">
@import "../../layouts/styles/styles.scss";
</style>
