import { route } from "quasar/wrappers";
import { createRouter, createWebHistory } from "vue-router";
import routes from "./routes";
import { api } from "../boot/axios";

export default route(function (/* { store, ssrContext } */) {
  const Router = createRouter({
    scrollBehavior: () => ({ left: 0, top: 0 }),
    routes,
    history: createWebHistory(process.env.VUE_APP_ROUTER_MODE || "history")
  });

  Router.beforeEach(async (to, from, next) => {
    const token = localStorage.getItem("token");
    const requiresAuth = to.matched.some((record) => record.meta.requiresAuth);

    if (requiresAuth) {
      if (!token) {
        next({ name: "loginPage" });
      } else {
        try {
          const response = await api.get("/api.php?validate_token=true", {
            headers: {
              Authorization: `Bearer ${token}`
            }
          });

          if (response.data.status === "success") {
            next();
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