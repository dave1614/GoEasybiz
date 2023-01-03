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
  mdiAlphaRBox,
  mdiInformation,
  mdiWallet,
  mdiCashFast,
  mdiAccountCash,
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
    ],
  },
  {
    route: "referral_link",
    label: "Referral Link",
    icon: mdiAlphaRBox,
  },
  {
    route: "earning_to_wallet_hist",
    label: "Earning To Wallet Hist.",
    icon: mdiCashFast,
  },
  {
    label: "Services",
    icon: mdiInformation,
    menu: [
      {
        route: "airtime_topup",
        label: "Airtime Topup",
      },
      {
        route: "internet_data",
        label: "Internet Data",
      },
      {
        route: "cable_tv",
        label: "Cable TV",
      },
      {
        route: "electricity_topup",
        label: "Electricity Topup",
      },
      {
        route: "airtime_to_wallet",
        label: "Airtime To Wallet",
      },
      {
        route: "router_recharge",
        label: "Router Recharge",
      },
      {
        route: "educational_vouchers",
        label: "Educational Vouchers",
      },
      
    ],
  },
  {
    label: "Easy Loan",
    icon: mdiAccountCash,
    menu: [
      {
        route: "apply_for_easy_loan_page",
        label: "Apply For Loan",
      },
      {
        route: "easy_loan_history_page",
        label: "View History",
      },
    ]
  },
  {
    label: "E-Wallet",
    icon: mdiWallet,
    menu: [
      {
        route: "credit_user_wallet",
        label: "Credit Wallet",
      },
      {
        route: "wallet_credit_history",
        label: "Wallet Credit History",
      },
      {
        route: "funds_transfer",
        label: "Funds Transfer",
      },
      {
        route: "transfer_history",
        label: "Transfer History",
      },
      {
        route: "funds_withdrawal",
        label: "Funds Withdrawal",
      },
      {
        route: "withdrawal_history",
        label: "Withdrawal History",
      },
      {
        route: "wallet_statement",
        label: "E-Wallet Statement",
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
