import { boot } from "quasar/wrappers";
     import axios from "axios";

     const api = axios.create({
       baseURL: process.env.VUE_APP_API_URL || "https://your-backend-placeholder.com/api",
       timeout: 5000,
     });

     export default boot(({ app }) => {
       app.config.globalProperties.$axios = axios;
       app.config.globalProperties.$api = api;
     });

     export { api };