// src/boot/axios.js
import { boot } from "quasar/wrappers";
import axios from "axios";

console.log('VUE_APP_API_URL:', process.env.VUE_APP_API_URL); // Debug

const api = axios.create({
  baseURL: process.env.VUE_APP_API_URL,
  timeout: 5000,
});

export default boot(({ app }) => {
  app.config.globalProperties.$axios = axios;
  app.config.globalProperties.$api = api;
});

export { api };