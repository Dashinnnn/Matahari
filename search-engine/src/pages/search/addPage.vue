<template>
  <q-page class="q-pa-md">
    <div>
      <q-btn dense borderless flat icon="arrow_back" @click="$router.go(-1)" />
    </div>
    <div class="q-pl-xl">
      <h5>Add Data</h5>

      <div class="q-pt-md parentCont">
        <q-card class="q-pa-xl information-card">
          <!--*FULLNAME INPUT FIELD*-->
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

          <!--*BARANGAY INPUT FIELD*-->
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

          <!--*DESIGNATION INPUT FIELD*-->
          <div class="q-pb-md">
            <label>Designation <span>*</span></label>
            <q-select
              class="dropdown"
              dense
              borderless
              hide-bottom-space
              v-model="designation"
              :options="[
                'P.O',
                'LCO',
                'TL-L1',
                'TL-L2',
                'POC-L1',
                'POC-L2',
                'ENFORCER',
                'DEPUTY',
                'SOCMED LEADER'
              ]"
              label="Select Designation"
              emit-value
              map-options
            />
          </div>

          <!--*PAYROLL INPUT FIELD*-->
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

          <!--*AREA OF RESPONSIBILITY INPUT FIELD*-->
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

          <!--*PRECINT INPUT FIELD*-->
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

          <!--*GEOPOINT INPUT FIELD*-->
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

          <!--*POLITICAL TENDENCY INPUT FIELD*-->
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

          <!--*INFLUENCE (FAMILY) INPUT FIELD*-->
          <div class="q-pb-md">
            <label>Sphere of Infuence (Family)</label>
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

          <!--*INFLUENCE (AFFILIATE) INPUT FIELD*-->
          <div class="q-pb-md">
            <label>Sphere of Infuence (Affiliate)</label>
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

          <!--*REMARKS INPUT FIELD*-->
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

          <!--*PHOTO UPLOAD INPUT FIELD*-->
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

          <!--*SUBMIT FORM*-->
          <div class="q-mt-lg">
            <q-btn
              label="Submit Data"
              color="secondary"
              @click="submitForm"
              class="addBtn"
            />
          </div>
        </q-card>
      </div>
    </div>
  </q-page>
</template>

<script>
import axios from "axios";
export default {
  data() {
    return {
      name: "",
      barangay: "",
      designation: "",
      aor: "",
      precinct: "",
      geopoint: "",
      pt: "",
      spf: "",
      spa: "",
      remarks: "",
      photo: null,
      payroll: "",
      congressman: "",
    };
  },

  methods: {
    onFileAdded(files) {
      if (files.length > 0) {
        const file = files[0];
        const allowedTypes = ["image/jpeg", "image/png"];
        if (allowedTypes.includes(file.type)) {
          this.photo = file;
        } else {
          alert("Invalid file type. Please upload a JPG or PNG image.");
        }
      }
    },

    validateForm() {
      if (
        !this.name.trim() ||
        !this.barangay.trim() ||
        !this.designation.trim()
      ) {
        alert("Please fill out all required fields.");
        return false;
      }
      return true;
    },

    submitForm() {
      if (!this.validateForm()) {
        return;
      }

      console.log("Payroll:", this.payroll);

      const formData = new FormData();
      formData.append("name", this.name || "");
      formData.append("barangay", this.barangay || "");
      formData.append("designation", this.designation || "");
      formData.append("payroll", this.payroll || "");
      formData.append("aor", this.aor || "");
      formData.append("precinct", this.precinct || "");
      formData.append("geopoint", this.geopoint || "");
      formData.append("sp_family", this.spf || "");
      formData.append("sp_affiliate", this.spa || "");
      formData.append("remarks", this.remarks || "");
      formData.append("pt", this.pt || "");
      formData.append("congressman", this.congressman || ""); // Fixed: was this.pt

      if (this.photo) {
        formData.append("photo", this.photo);
      }

      axios
        .post("http://localhost/searchEngine/api.php", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          },
        })
        .then((response) => {
          console.log("Response:", response.data);
          this.$router.push({ name: "home" });
          this.$q.notify({
            type: "positive",
            message: "Data added successfully!",
            position: "bottom-right",
          });
        })
        .catch((error) => {
          console.error("Error adding data:", error);
          if (error.response) {
            console.error("Error response:", error.response.data); // Debug
          }
        });
    },

    resetForm() {
      this.name = "";
      this.barangay = "";
      this.designation = "";
      this.payroll = "";
      this.aor = "";
      this.precinct = "";
      this.geopoint = "";
      this.pt = "";
      this.spf = "";
      this.spa = "";
      this.photo = null;
      this.congressman = "";
    },
  },
};
</script>

<style lang="scss">
@import "../../layouts/styles/styles.scss";
</style>
