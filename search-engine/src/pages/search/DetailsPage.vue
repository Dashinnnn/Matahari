<template>
  <q-page class="q-pa-md">
    <div>
      <q-btn dense borderless flat icon="arrow_back" @click="$router.go(-1)" />
    </div>
    <div class="q-pl-xl">
      <div class="center">
        <h3>Personal Information</h3>
      </div>

      <div class="center">
        <q-card class="information-card">
          <q-card-section>
            <div class="center">
              <!-- PHOTO SECTION -->
              <div class="q-pb-md q-pl-lg q-pt-md photo" v-if="details.photo">
                <img :src="details.photo" alt="A person's Photo" />
              </div>
              <div class="q-pb-md q-pl-lg photo" v-else>
                <span class="bold-text">Photo: No Photo Available</span>
              </div>
            </div>
            <!--NAME SECTION-->
            <div class="text-h3 q-pt-md q-pb-md center name">
              {{ details.name || "N/A" }}
            </div>

            <div class="q-pl-xl">
              <!--DESGINATION SECTION-->
              <div class="q-pb-md">
                <span class="bold-text">Designation: </span>
                <span class="dataProfile">{{
                  details.designation || "No Designation Available"
                }}</span>
              </div>
              <!--BARANGAY SECTION-->
              <div class="q-pb-md">
                <span class="bold-text">Baranagay: </span>
                <span class="dataProfile">{{ details.barangay || "N/A" }}</span>
              </div>
              <!--AOR SECTION-->
              <div class="q-pb-md">
                <span class="bold-text">Area of Responsibility: </span>
                <span class="dataProfile">{{ details.aor || "N/A" }}</span>
              </div>
              <!--PRECINCT SECTION-->
              <div class="q-pb-md">
                <span class="bold-text">Precinct: </span>
                <span class="dataProfile">{{ details.precinct || "N/A" }}</span>
              </div>
              <!--PAYROLL SECTION-->
              <div class="q-pb-md">
                <span class="bold-text">Payroll: </span>
                <span class="dataProfile">{{ details.payroll || "N/A" }}</span>
              </div>
              <!--POLITICAL TENDENCY-->
              <div class="q-pb-md">
                <span class="bold-text">Political Tendency: </span>
                <span class="dataProfile">{{ details.pt || "N/A" }}</span>
              </div>

              <!--POLITICAL TENDENCY-->
              <div class="q-pb-md">
                <span class="bold-text">Congressman: </span>
                <span class="dataProfile">{{
                  details.congressman || "N/A"
                }}</span>
              </div>

              <!--GEOPOINT SECTION-->
              <div class="q-pb-md">
                <span class="bold-text">Geopoint: </span>
                <span class="dataProfile">{{ details.geopoint || "N/A" }}</span>
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <!--SPHERE OF INFLUNCE (FAMILY) SECTION-->
      <div class="center q-mt-md">
        <q-card class="information-card">
          <q-card-section>
            <div>
              <h5>Sphere of Influence (Family)</h5>
            </div>
            <div class="q-pb-md">
              <span class="bold-text">Sphere of Influence (Family): </span>
              <span class="dataProfile">{{ details.sp_family || "N/A" }}</span>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <!--SPHERE OF INFLUNCE (FAMILY) SECTION-->
      <div class="center q-mt-md">
        <q-card class="information-card">
          <q-card-section>
            <div>
              <h5>Sphere of Influence (Affiliate)</h5>
              <div class="q-pb-md">
                <span class="bold-text"
                  >Sphere of Influence (Affiliates):
                </span>
                <span class="dataProfile">{{
                  details.sp_affiliate || "N/A"
                }}</span>
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <!--SPHERE OF INFLUNCE (FAMILY) SECTION-->
      <div class="center q-mt-md">
        <q-card class="information-card">
          <q-card-section>
            <div>
              <h5>Remarks</h5>
              <div class="q-pb-md">
                <span class="bold-text">Remarks: </span>
                <span class="dataProfile">{{ details.remarks || "N/A" }}</span>
              </div>
            </div>
          </q-card-section>
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
      details: {},
    };
  },
  methods: {
    fetchDetails() {
      const id = this.$route.params.id;
      console.log("Fetching details for ID: ", id);

      api
        .get(`?id=${id}`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          },
        })
        .then((response) => {
          console.log("API response: ", response.data);
          if (
            response.data.status === "success" &&
            response.data.data.length > 0
          ) {
            this.details = response.data.data[0];
          } else {
            console.error("No details found for ID: ", id);
            this.details = {};
          }
        })
        .catch((error) => {
          console.error("Error fetching details:", error);
          if (error.response && error.response.status === 401) {
            this.$q.notify({
              type: "negative",
              message: "Session expired. Please log in again.",
              position: "bottom-right",
            });
            this.$router.push({ name: "loginPage" });
          }
        });
    },
  },
  mounted() {
    this.fetchDetails();
  },
};
</script>

<style lang="scss">
@import "../../layouts/styles/styles.scss";
</style>
