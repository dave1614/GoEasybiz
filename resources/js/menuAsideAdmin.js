import {
  mdiAccountCircle,
  mdiMonitor,
  mdiGithub,
  mdiLock,
  mdiAlertCircle,
  mdiSquareEditOutline,
  mdiTable,
  mdiViewList,
  mdiTelevisionGuide,
  mdiResponsive,
  mdiPalette,
  mdiHospitalBuilding,
  mdiSquareCircle,
  mdiWalletPlus,
  mdiWallet,
  mdiBookAccount,
  mdiChartBar,
} from "@mdi/js";


export default [
  {
    route: "dashboard",
    icon: mdiMonitor,
    label: "Dashboard",
  },
  {
    label: "Profile",
    icon: mdiAccountCircle,
    menu: [
      {
        route: "edit_profile",
        label: "Edit Profile",
      },
      {
        route: "change_password",
        label: "Change Password",
      },
    ],
  },
  {
    route: "airtime_to_wallet_records",
    icon: mdiMonitor,
    label: "Airtime To Wallet",
  },
  {
    label: "Genealogy",
    icon: mdiSquareCircle,
    menu: [
      {
        route: "genealogy_tree",
        label: "Genealogy Tree",
      },
      {
        route: "sponsor_tree",
        label: "Sponsor Tree",
      },
      {
        route: "downline_members",
        label: "Downline Members",
      },
    ],
  },
  // {
  //   route: "account_credit_requests",
  //   icon: mdiWalletPlus,
  //   label: "Acct Credit Reqs",
  // },
  {
    route: "account_withdrawal_requests",
    icon: mdiWallet,
    label: "Acct With. Reqs",
  },
  {
    label: "User Mgt.",
    icon: mdiBookAccount,
    menu: [
      {
        route: "view_members_list",
        label: "Members List",
      },
      
    ],
  },
  {
    label: "Reports",
    icon: mdiChartBar,
    menu: [
      {
        route: "airtime_combo_requests",
        label: "Airtime Combo Requests",
      },
      {
        route: "data_combo_requests",
        label: "Data Combo Requests",
      },
      {
        route: "account_credit_history",
        label: "Account Credit Hist.",
      },
      {
        route: "admin_account_credit_history",
        label: "Admin Acct Credit Hist.",
      },
      {
        route: "account_debit_history",
        label: "Admin Acct Debit Hist.",
      },
      

    ],
  },
  // {
  //   route: "tables",
  //   label: "Tables",
  //   icon: mdiTable,
  // },
  // {
  //   route: "forms",
  //   label: "Forms",
  //   icon: mdiSquareEditOutline,
  // },
  // {
  //   route: "ui",
  //   label: "UI",
  //   icon: mdiTelevisionGuide,
  // },
  // {
  //   route: "responsive",
  //   label: "Responsive",
  //   icon: mdiResponsive,
  // },
  // {
  //   to: "/",
  //   label: "Styles",
  //   icon: mdiPalette,
  // },
  // {
  //   route: "profile",
  //   label: "Profile",
  //   icon: mdiAccountCircle,
  // },
  // {
  //   route: "sign_in",
  //   label: "Login",
  //   icon: mdiLock,
  // },
  // {
  //   route: "error",
  //   label: "Error",
  //   icon: mdiAlertCircle,
  // },
  // {
  //   label: "Dropdown",
  //   icon: mdiViewList,
  //   menu: [
  //     {
  //       label: "Item One",
  //     },
  //     {
  //       label: "Item Two",
  //     },
  //   ],
  // },
  // {
  //   href: "https://github.com/justboil/admin-one-vue-tailwind",
  //   label: "GitHub",
  //   icon: mdiGithub,
  //   target: "_blank",
  // },
];
