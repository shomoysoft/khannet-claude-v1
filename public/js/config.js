/* ============================================================
   KhanNet / Link3 — Site Configuration
   ============================================================
   THIS is the only file you need to edit for business details.
   Changes here automatically update everywhere on the site.
   ============================================================ */

const SITE = {

  /* ── Brand ── */
  name:     'KhanNet',
  fullName: 'KhanNet / Link3',
  tagline:  'Fast Internet for Magura',

  /* ── Contact ─────────────────────────────────────────────
     phone     : displayed number (shown in text on pages)
     phoneIntl : full international format for tel: links (no + sign)
     whatsapp  : number for wa.me links (no + sign)
  ───────────────────────────────────────────────────────── */
  phone:        '01X-XXXX-XXXX',
  phoneIntl:    '8801XXXXXXXXX',
  whatsapp:     '8801XXXXXXXXX',

  email:        'info@khannet.com.bd',
  supportEmail: 'support@khannet.com.bd',

  /* ── Social ── */
  facebook: '#',   // Replace with: https://facebook.com/yourpage

  /* ── Address ── */
  address: {
    street:   'Shibrampur',
    district: 'Magura',
    country:  'Bangladesh',
    full:     'Shibrampur, Magura, Bangladesh',
  },

  /* ── Coverage areas ──────────────────────────────────────
     Add or remove areas from this list.
     All area dropdowns on the site update automatically.
  ───────────────────────────────────────────────────────── */
  areas: [
    'Shibrampur',
    'Doarpar',
    'Atharkhada',
  ],

  /* ── Business hours ── */
  hours: {
    weekdays:  'Saturday – Thursday: 9:00 AM – 9:00 PM',
    friday:    'Friday: 2:00 PM – 9:00 PM',
    emergency: '24/7 via WhatsApp',
  },

  /* ── Form integration ── */
  web3formsKey: 'YOUR_WEB3FORMS_ACCESS_KEY', // Get free key at web3forms.com

  /* ── Plans ───────────────────────────────────────────────
     Real Link3 package plans (speeds converted from Kbps).
     The plan dropdown on the contact form & plans page
     update automatically from this list.

     speed  : download in Mbps
     price  : monthly fee in BDT ৳
     popular: shows "Most Popular" badge on plans page
  ───────────────────────────────────────────────────────── */
  plans: {
    home: [
      {
        name:    'Starter',
        speed:   20,
        price:   525,
        popular: false,
        features: ['20 Mbps Download', 'Unlimited Data', 'Free Installation', '24/7 Local Support'],
      },
      {
        name:    'Starter Up Booster',
        speed:   25,
        price:   599,
        popular: false,
        features: ['25 Mbps Download', 'Unlimited Data', 'Free Installation', '24/7 Local Support'],
      },
      {
        name:    'Simple',
        speed:   30,
        price:   650,
        popular: false,
        features: ['30 Mbps Download', 'Unlimited Data', 'Free Installation', '24/7 Local Support'],
      },
      {
        name:    'Simple Up Booster',
        speed:   35,
        price:   699,
        popular: false,
        features: ['35 Mbps Download', 'Unlimited Data', 'Free Installation', '24/7 Local Support'],
      },
      {
        name:    'Simple Plus',
        speed:   38,
        price:   750,
        popular: false,
        features: ['38 Mbps Download', 'Unlimited Data', 'Free Installation', '24/7 Local Support'],
      },
      {
        name:    'Surfer Prime',
        speed:   40,
        price:   825,
        popular: true,
        features: ['40 Mbps Download', 'Unlimited Data', 'Free Installation', 'Priority Support'],
      },
      {
        name:    'Surfer Advance',
        speed:   45,
        price:   890,
        popular: false,
        features: ['45 Mbps Download', 'Unlimited Data', 'Free Installation', 'Priority Support'],
      },
      {
        name:    'Surfer Plus',
        speed:   80,
        price:   1050,
        popular: false,
        features: ['80 Mbps Download', 'Unlimited Data', 'Free Installation', 'Priority Support'],
      },
      {
        name:    'Surfer Up Booster',
        speed:   85,
        price:   1099,
        popular: false,
        features: ['85 Mbps Download', 'Unlimited Data', 'Free Installation', 'Priority Support'],
      },
      {
        name:    'Cheetah Prime',
        speed:   100,
        price:   1275,
        popular: true,
        features: ['100 Mbps Download', 'Unlimited Data', 'Free Installation', 'Dedicated Support Line'],
      },
      {
        name:    'Cheetah Up Booster',
        speed:   103,
        price:   1319,
        popular: false,
        features: ['103 Mbps Download', 'Unlimited Data', 'Free Installation', 'Dedicated Support Line'],
      },
      {
        name:    'Group Student Pack',
        speed:   110,
        price:   1375,
        popular: false,
        features: ['110 Mbps Download', 'Unlimited Data', 'Free Installation', 'Student Discount Applied'],
      },
      {
        name:    'Gamers',
        speed:   120,
        price:   1475,
        popular: false,
        features: ['120 Mbps Download', 'Low Latency', 'Unlimited Data', 'Free Installation'],
      },
      {
        name:    'Eagle Advance',
        speed:   130,
        price:   1700,
        popular: false,
        features: ['130 Mbps Download', 'Unlimited Data', 'Free Installation', 'Dedicated Support Line'],
      },
      {
        name:    'Hawk Lite',
        speed:   135,
        price:   1800,
        popular: false,
        features: ['135 Mbps Download', 'Unlimited Data', 'Free Installation', 'Dedicated Support Line'],
      },
      {
        name:    'Hawk Advance',
        speed:   140,
        price:   1900,
        popular: false,
        features: ['140 Mbps Download', 'Unlimited Data', 'Free Installation', 'Dedicated Support Line'],
      },
      {
        name:    'Swift Prime',
        speed:   150,
        price:   2525,
        popular: false,
        features: ['150 Mbps Download', 'Unlimited Data', 'Free Installation', 'Free Static IP', 'VIP Support'],
      },
      {
        name:    'Swift Advance',
        speed:   160,
        price:   2800,
        popular: false,
        features: ['160 Mbps Download', 'Unlimited Data', 'Free Installation', 'Free Static IP', 'VIP Support'],
      },
      {
        name:    'Extreme Advance',
        speed:   210,
        price:   4625,
        popular: false,
        features: ['210 Mbps Download', 'Unlimited Data', 'Free Installation', 'Free Static IP', 'Dedicated Account Manager'],
      },
    ],
    business: [
      {
        name:    'Employee Pack',
        speed:   50,
        price:   500,
        popular: false,
        features: ['50 Mbps Download', 'Unlimited Data', 'Free Installation', 'Corporate Billing', '24/7 Support'],
      },
      {
        name:    'SmartShop Pack',
        speed:   50,
        price:   899,
        popular: true,
        features: ['50 Mbps Download', 'Unlimited Data', 'Free Installation', 'Business Support', 'Free Static IP'],
      },
      {
        name:    'EDC Project',
        speed:   20,
        price:   1260,
        popular: false,
        features: ['20 Mbps Dedicated', 'Unlimited Data', 'Free Installation', 'SLA Guaranteed', 'Free Static IP'],
      },
    ],
  },

};
