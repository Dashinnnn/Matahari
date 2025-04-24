<template>
  <q-page class="flex flex-center">
    <q-card class="q-pa-md loginCont" style="width: 300px" >
      <q-card-section>
        <div class="text-h6">Login</div>
      </q-card-section>

      <q-card-section>
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
        <q-btn color="primary" label="Login" @click="login" />
        <q-btn color="secondary" label="Register" @click="goToRegister" flat />
      </q-card-actions>
    </q-card>
  </q-page>
</template>
<script>
import axios from "axios";
export default {
  data() {
    return {
      username: "",
      password: "",
    };
  },
  methods: {
    async login() {
      try {
        const response = await this.$axios.post(
          "http://localhost/searchEngine/login.php",
          {
            username: this.username,
            password: this.password,
          }
        );

        console.log("Login response: ", response.data);

        if (response.data.success) {
          localStorage.setItem("token", response.data.token);
          localStorage.setItem("role", response.data.data.role);
          this.$q.notify({
            type: "positive",
            message: "Login successful",
            position: "bottom-right",
          });
          this.$router.push({ name: "home" });
        } else {
          this.$q.notify({
            type: "negative",
            message: response.data.message,
            position: "bottom-right",
          });
        }
      } catch (error) {
        console.error("Error logging in: ", error);
        this.$q.notify({
          type: "negative",
          message: "An error occurred during login.",
          position: "bottom-right",
        });
      }
    },

    goToRegister() {
      this.$router.push({ name: "registerPage" });
    },
  },
};
</script>

<style lang="scss">
@import "../../layouts/styles/login.scss";
</style>
