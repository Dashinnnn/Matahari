const routes = [
  {
    path: "/",
    component: () => import("layouts/MainLayout.vue"),
    children: [
      {
        path: "",
        name: "loginPage",
        component: () => import("../pages/accounts/loginPage.vue")
      },
      {
        path: "registerPage",
        name: "registerPage",
        component: () => import("../pages/accounts/registerPage.vue")
      },
      {
        path: "home",
        name: "home",
        component: () => import("../pages/search/IndexPage.vue"),
        meta: { requiresAuth: true }
      },
      {
        path: "/details/:id",
        name: "details",
        component: () => import("../pages/search/DetailsPage.vue"),
        meta: { requiresAuth: true }
      },
      {
        path: "addPage",
        name: "addPage",
        component: () => import("../pages/search/addPage.vue"),
        meta: { requiresAuth: true }
      },
      {
        path: "edit/:id",
        name: "editPage",
        component: () => import("../pages/search/editPage.vue"),
        meta: { requiresAuth: true }
      },
      {
        path: "manageAccs",
        name: "manageAccs",
        component: () => import("../pages/accounts/manageAccs.vue"),
        meta: { requiresAuth: true }
      },
      {
        path: "recycleBin",
        name: "recycleBin",
        component: () => import("../pages/search/recycleBin.vue"),
        meta: { requiresAuth: true }
      }
    ]
  },
  {
    path: "/:catchAll(.*)*",
    name: "ErrorNotFound",
    component: () => import("pages/ErrorNotFound.vue")
  }
];

export default routes;