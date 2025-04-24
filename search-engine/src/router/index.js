import { route } from "quasar/wrappers";
import {
  createRouter,
  createMemoryHistory,
  createWebHistory,
  createWebHashHistory,
} from "vue-router";
import routes from "./routes";
import axios from "axios"; // Ensure axios is imported

export default route(function (/* { store, ssrContext } */) {
  const createHistory = process.env.SERVER
    ? createMemoryHistory
    : process.env.VUE_ROUTER_MODE === "history"
    ? createWebHistory
    : createWebHashHistory;

  const Router = createRouter({
    scrollBehavior: () => ({ left: 0, top: 0 }),
    routes,
    history: createHistory(process.env.VUE_ROUTER_BASE),
  });

  Router.beforeEach(async (to, from, next) => {
    const token = localStorage.getItem("token");
    const requiresAuth = to.matched.some((record) => record.meta.requiresAuth);

    if (requiresAuth) {
      if (!token) {
        next({ name: "loginPage" });
      } else {
        try {
          const response = await axios.get(
            "http://localhost/searchEngine/api.php?validate_token=true", // Use api.php for consistency
            {
              headers: {
                Authorization: `Bearer ${token}`,
              },
            }
          );

          if (response.data.status === "success") {
            next(); // Token is valid, proceed
          } else {
            localStorage.removeItem("token");
            next({ name: "loginPage" });
          }
        } catch (error) {
          console.error("Token validation error:", error);
          localStorage.removeItem("token");
          next({ name: "loginPage" });
        }
      }
    } else {
      next();
    }
  });

  return Router;
});