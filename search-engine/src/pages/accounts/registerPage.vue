<template>
  <q-page class="flex flex-center">
    <q-card class="q-pa-md loginCont" style="width: 400px">
      <q-card-section>
        <div class="text-h6">Register</div>
      </q-card-section>

      <q-card-section>
        <q-input v-model="fname" label="First Name" filled class="inputData" />
        <q-input v-model="lname" label="Last Name" filled class="inputData" />
        <q-input v-model="username" label="Username" filled class="inputData" />
        <q-input
          v-model="password"
          label="Password"
          type="password"
          filled
          class="inputData"
        />
      </q-card-section>

      <q-card-actions align="center">
        <q-btn color="primary" label="Register" @click="register" />
        <q-btn color="secondary" label="Login" @click="goToLogin" flat />
      </q-card-actions>
    </q-card>
  </q-page>
</template>
<script>
import axios from "axios";
export default {
  data() {
    return {
      fname: "",
      lname: "",
      username: "",
      password: "",
    };
  },
  methods: {
    register() {
      axios
        .post("http://localhost/searchEngine/register.php", {
          fname: this.fname,
          lname: this.lname,
          username: this.username,
          password: this.password,
        })
        .then((response) => {
          console.log("Response:", response.data);
          if (response.data.success) {
            this.$q.notify({
              type: "positive",
              message: "Registration successful! Please wait for verification.",
              position: "bottom-right",
            });
            this.$router.push({ name: "loginPage" });
          } else {
            this.$q.notify({ type: "negative", message: "Register failed" });
          }
        })
        .catch((error) => {
          console.error("Registration Error:", error);
          this.$q.notify({
            type: "negative",
            message: "An error occured during registration.",
          });
        });
    },

    goToLogin() {
      this.$router.push({ name: "loginPage" });
    },
  },
};
</script>

<style lang="scss">
@import "../../layouts/styles/login.scss";
</style>
