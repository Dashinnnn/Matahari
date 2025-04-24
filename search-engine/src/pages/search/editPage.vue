<template>
  <q-page class="q-pa-md">
    <div>
      <q-btn dense borderless flat icon="arrow_back" @click="$router.go(-1)" />
    </div>
    <div class="q-pl-xl">
      <h5>Edit Data</h5>

      <div class="q-pt-md parentCont">
        <q-card class="q-pa-xl information-card">
          <!-- FULLNAME INPUT FIELD -->
          <div class="q-pb-md">
            <label>Fullname <span>*</span></label>
            <q-input
              type="text"
              class="inputField"
              placeholder="Enter Fullname"
              dense
              borderless
              hide-bottom-space
              v-model="name"
            />
          </div>

          <!-- BARANGAY INPUT FIELD -->
          <div class="q-pb-md">
            <label>Barangay <span>*</span></label>
            <q-select
              class="dropdown"
              dense
              borderless
              hide-bottom-space
              v-model="barangay"
              :options="[
                'Bagong Tubig',
                'Baclas',
                'Balimbing',
                'Bambang',
                'Barangay 1 (Poblacion)',
                'Barangay 2 (Poblacion)',
                'Barangay 3 (Poblacion)',
                'Barangay 4 (Poblacion)',
                'Barangay 5 (Poblacion)',
                'Barangay 6 (Poblacion)',
                'Bisaya',
                'Cahil',
                'Caluangan',
                'Calantas',
                'Camastilisan',
                'Coral ni Lopez (Sugod)',
                'Coral ni Bacal',
                'Dacanlao',
                'Dila',
                'Loma',
                'Lumbang Calzada',
                'Lumbang na Bata',
                'Lumbang na Matanda',
                'Madalunot',
                'Makina,',
                'Matipok',
                'Munting Coral',
                'Niyugan',
                'Pantay',
                'Puting Bato West',
                'Puting Kahoy',
                'Puting Bato East',
                'Quisumbing',
                'Salong',
                'San Rafael',
                'Sinisian',
                'Taklang Anak',
                'Talisay',
                'Tamayo',
                'Timbain',
              ]"
              label="Select Barangay"
              emit-value
              map-options
            />
          </div>

          <!-- DESIGNATION INPUT FIELD -->
          <div class="q-pb-md">
            <label>Designation <span>*</span></label>
            <q-select
              class="dropdown"
              dense
              borderless
              hide-bottom-space
              v-model="designation"
              :options="['P.O', 'LCO', 'TL-L1', 'TL-L2', 'POC-L1', 'POC-L2']"
              label="Select Designation"
              emit-value
              map-options
            />
          </div>

          <!-- PAYROLL INPUT FIELD -->
          <div class="q-pb-md">
            <label>Payroll</label>
            <div>
              <q-radio
                v-model="payroll"
                val="YES"
                label="Yes"
                class="q-mr-xl q-ml-xl"
              />
              <q-radio
                v-model="payroll"
                val="NO"
                label="No"
                class="q-mr-xl q-ml-xl"
              />
            </div>
          </div>

          <!-- AREA OF RESPONSIBILITY INPUT FIELD -->
          <div class="q-pb-md">
            <label>Area of Responsibility</label>
            <q-input
              type="text"
              class="inputField"
              placeholder="Enter Area of Responsibility"
              dense
              hide-bottom-space
              borderless
              v-model="aor"
            />
          </div>

          <!-- PRECINCT INPUT FIELD -->
          <div class="q-pb-md">
            <label>Precinct</label>
            <q-input
              type="text"
              class="inputField"
              placeholder="Enter Precinct"
              dense
              hide-bottom-space
              borderless
              v-model="precinct"
            />
          </div>

          <!-- GEOPOINT INPUT FIELD -->
          <div class="q-pb-md">
            <label>Geopoint</label>
            <q-input
              type="text"
              class="inputField"
              placeholder="Enter Geopoint"
              dense
              hide-bottom-space
              borderless
              v-model="geopoint"
            />
          </div>

          <!-- POLITICAL TENDENCY INPUT FIELD -->
          <div class="q-pb-md">
            <label>Political Tendency</label>
            <q-input
              type="text"
              class="inputField"
              placeholder="Enter Political Tendency"
              dense
              hide-bottom-space
              borderless
              v-model="pt"
            />
          </div>

          <!--*CONGRESSMAN INPUT FIELD*-->
          <div class="q-pb-md">
            <label>Congressman <span>*</span></label>
            <q-select
              class="dropdown"
              dense
              borderless
              hide-bottom-space
              v-model="congressman"
              :options="[
                'ERIC BUHAIN',
                'LEANDRO LEGARDA LEVISTE',
                'INCONCLUSIVE',
              ]"
              label="Select congressman"
              emit-value
              map-options
            />
          </div>

          <!-- INFLUENCE (FAMILY) INPUT FIELD -->
          <div class="q-pb-md">
            <label>Sphere of Influence (Family)</label>
            <q-input
              type="text"
              class="inputField"
              placeholder="Enter family that can be influenced"
              dense
              hide-bottom-space
              borderless
              v-model="spf"
            />
          </div>

          <!-- INFLUENCE (AFFILIATE) INPUT FIELD -->
          <div class="q-pb-md">
            <label>Sphere of Influence (Affiliate)</label>
            <q-input
              type="text"
              class="inputField"
              placeholder="Enter affiliates that can be influenced"
              dense
              hide-bottom-space
              borderless
              v-model="spa"
            />
          </div>

          <!-- REMARKS INPUT FIELD -->
          <div class="q-pb-md">
            <label>Remarks</label>
            <q-input
              type="text"
              class="inputField"
              placeholder="Enter remarks"
              dense
              hide-bottom-space
              borderless
              v-model="remarks"
            />
          </div>

          <!-- PHOTO UPLOAD INPUT FIELD -->
          <label>Photo</label>
          <q-uploader
            v-model="photo"
            label="Upload Photo"
            accept="image/*"
            @added="onFileAdded"
            :auto-upload="false"
            hide-upload-button
            filled
            color="secondary"
            square
          />

          <!-- SUBMIT FORM -->
          <div>
            <q-btn
              label="Update Data"
              color="secondary"
              @click="updateRow"
              class="addBtn"
            />
          </div>
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script>
import { api } from "boot/axios"; 

export default {
  data() {
    return {
      name: "",
      barangay: "",
      designation: "",
      payroll: "",
      aor: "",
      precinct: "",
      geopoint: "",
      pt: "",
      spf: "",
      spa: "",
      remarks: "",
      congressman: "",
      photo: null,
    };
  },

  created() {
    const id = this.$route.params.id;
    if (id) {
      this.loadData(id);
      console.log("Editing mode, ID:", id);
    }
  },

  methods: {
    async loadData(id) {
      try {
        const response = await api.get(`?id=${id}`, {
          headers: { Authorization: `Bearer ${localStorage.getItem("token")}` },
        });
        if (
          response.data.status === "success" &&
          response.data.data.length > 0
        ) {
          const data = response.data.data[0];
          this.name = data.name;
          this.barangay = data.barangay;
          this.designation = data.designation;
          this.payroll = data.payroll;
          this.aor = data.aor;
          this.precinct = data.precinct;
          this.geopoint = data.geopoint;
          this.pt = data.pt;
          this.spf = data.sp_family;
          this.spa = data.sp_affiliate;
          this.remarks = data.remarks;
          this.congressman = data.congressman;
          this.photo = data.photo ? [{ name: data.photo, url: data.photo }] : null; 
        } else {
          console.error("No data found for the given ID");
        }
      } catch (error) {
        console.error("Error loading data:", error);
      }
    },

    onFileAdded(files) {
      if (files.length > 0) {
        const file = files[0];
        const allowedTypes = ["image/jpeg", "image/png"]; 
        if (allowedTypes.includes(file.type)) {
          this.photo = file;
        } else {
          this.$q.notify({
            type: "negative",
            message: "Invalid file type. Please upload a JPG or PNG image",
            position: "bottom-right",
          });
          this.photo = null;
        }
      }
    },

    validateForm() {
      if (
        !this.name.trim() ||
        !this.barangay.trim() ||
        !this.designation.trim()
      ) {
        this.$q.notify({
          type: "negative",
          message: "Please fill out all required fields (marked with *).",
          position: "bottom-right",
        });
        return false;
      }
      return true;
    },

    async updateRow() {
      const id = this.$route.params.id;

      if (!id) {
        this.$q.notify({
          type: "negative",
          message: "No ID found for the update.",
          position: "bottom-right",
        });
        return;
      }

      if (!this.validateForm()) {
        return;
      }

      const formData = new FormData();
      formData.append("action", "update");
      formData.append("id", id);
      formData.append("name", this.name);
      formData.append("barangay", this.barangay);
      formData.append("designation", this.designation);
      formData.append("payroll", this.payroll || "NO"); 
      formData.append("aor", this.aor || "");
      formData.append("precinct", this.precinct || "");
      formData.append("geopoint", this.geopoint || "");
      formData.append("sp_family", this.spf || "");
      formData.append("sp_affiliate", this.spa || "");
      formData.append("pt", this.pt || "");
      formData.append("remarks", this.remarks || "");
      formData.append("congressman", this.congressman || "");

      // Append photo if a new file is selected
      if (this.photo && this.photo instanceof File) {
        formData.append("photo", this.photo);
      }

      try {
        const response = await api.put("", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          },
        });
        if (response.data.status === "success") {
          this.$q.notify({
            type: "positive",
            message: "Data updated successfully",
            position: "bottom-right",
          });
          this.$router.push({ name: "home" });
        } else {
          this.$q.notify({
            type: "negative",
            message: response.data.message || "Error updating data",
            position: "bottom-right",
          });
        }
      } catch (error) {
        console.error("Error occurred while updating the data:", error);
        this.$q.notify({
          type: "negative",
          message: "Error occurred while updating the data",
          position: "bottom-right",
        });
      }
    },
  },
};
</script>

<style lang="scss">
@import "../../layouts/styles/styles.scss";
</style>