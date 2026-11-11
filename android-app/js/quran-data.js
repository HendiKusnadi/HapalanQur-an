// Data Referensi 114 Surah Al-Qur'an Standar Madinah
const QURAN_SURAHS = {
    "1": {
        "id": 1,
        "nama": "Al-Fatihah",
        "arab": "الفاتحة",
        "arti": "Pembukaan",
        "ayat": 7,
        "turun": "Makkiyah",
        "hal_mulai": 1,
        "hal_selesai": 1,
        "juz": 1
    },
    "2": {
        "id": 2,
        "nama": "Al-Baqarah",
        "arab": "البقرة",
        "arti": "Sapi Betina",
        "ayat": 286,
        "turun": "Madaniyah",
        "hal_mulai": 2,
        "hal_selesai": 49,
        "juz": 1
    },
    "3": {
        "id": 3,
        "nama": "Ali 'Imran",
        "arab": "آل عمران",
        "arti": "Keluarga Imran",
        "ayat": 200,
        "turun": "Madaniyah",
        "hal_mulai": 50,
        "hal_selesai": 76,
        "juz": 3
    },
    "4": {
        "id": 4,
        "nama": "An-Nisa'",
        "arab": "النساء",
        "arti": "Wanita",
        "ayat": 176,
        "turun": "Madaniyah",
        "hal_mulai": 77,
        "hal_selesai": 106,
        "juz": 4
    },
    "5": {
        "id": 5,
        "nama": "Al-Ma'idah",
        "arab": "المائدة",
        "arti": "Hidangan",
        "ayat": 120,
        "turun": "Madaniyah",
        "hal_mulai": 106,
        "hal_selesai": 127,
        "juz": 6
    },
    "6": {
        "id": 6,
        "nama": "Al-An'am",
        "arab": "الأنعام",
        "arti": "Binatang Ternak",
        "ayat": 165,
        "turun": "Makkiyah",
        "hal_mulai": 128,
        "hal_selesai": 150,
        "juz": 7
    },
    "7": {
        "id": 7,
        "nama": "Al-A'raf",
        "arab": "الأعراف",
        "arti": "Tempat yang Tertinggi",
        "ayat": 206,
        "turun": "Makkiyah",
        "hal_mulai": 151,
        "hal_selesai": 176,
        "juz": 8
    },
    "8": {
        "id": 8,
        "nama": "Al-Anfal",
        "arab": "الأنفال",
        "arti": "Harta Rampasan Perang",
        "ayat": 75,
        "turun": "Madaniyah",
        "hal_mulai": 177,
        "hal_selesai": 186,
        "juz": 9
    },
    "9": {
        "id": 9,
        "nama": "At-Taubah",
        "arab": "التوبة",
        "arti": "Pengampunan",
        "ayat": 129,
        "turun": "Madaniyah",
        "hal_mulai": 187,
        "hal_selesai": 207,
        "juz": 10
    },
    "10": {
        "id": 10,
        "nama": "Yunus",
        "arab": "يونس",
        "arti": "Nabi Yunus",
        "ayat": 109,
        "turun": "Makkiyah",
        "hal_mulai": 208,
        "hal_selesai": 221,
        "juz": 11
    },
    "11": {
        "id": 11,
        "nama": "Hud",
        "arab": "هود",
        "arti": "Nabi Hud",
        "ayat": 123,
        "turun": "Makkiyah",
        "hal_mulai": 221,
        "hal_selesai": 235,
        "juz": 11
    },
    "12": {
        "id": 12,
        "nama": "Yusuf",
        "arab": "يوسف",
        "arti": "Nabi Yusuf",
        "ayat": 111,
        "turun": "Makkiyah",
        "hal_mulai": 235,
        "hal_selesai": 248,
        "juz": 12
    },
    "13": {
        "id": 13,
        "nama": "Ar-Ra'd",
        "arab": "الرعد",
        "arti": "Guruh",
        "ayat": 43,
        "turun": "Madaniyah",
        "hal_mulai": 249,
        "hal_selesai": 255,
        "juz": 13
    },
    "14": {
        "id": 14,
        "nama": "Ibrahim",
        "arab": "إبراهيم",
        "arti": "Nabi Ibrahim",
        "ayat": 52,
        "turun": "Makkiyah",
        "hal_mulai": 255,
        "hal_selesai": 261,
        "juz": 13
    },
    "15": {
        "id": 15,
        "nama": "Al-Hijr",
        "arab": "الحجر",
        "arti": "Daerah Pegunungan",
        "ayat": 99,
        "turun": "Makkiyah",
        "hal_mulai": 262,
        "hal_selesai": 267,
        "juz": 14
    },
    "16": {
        "id": 16,
        "nama": "An-Nahl",
        "arab": "النحل",
        "arti": "Lebah",
        "ayat": 128,
        "turun": "Makkiyah",
        "hal_mulai": 267,
        "hal_selesai": 281,
        "juz": 14
    },
    "17": {
        "id": 17,
        "nama": "Al-Isra'",
        "arab": "الإسراء",
        "arti": "Memperjalankan Malam Hari",
        "ayat": 111,
        "turun": "Makkiyah",
        "hal_mulai": 282,
        "hal_selesai": 293,
        "juz": 15
    },
    "18": {
        "id": 18,
        "nama": "Al-Kahf",
        "arab": "الكهف",
        "arti": "Gua",
        "ayat": 110,
        "turun": "Makkiyah",
        "hal_mulai": 293,
        "hal_selesai": 304,
        "juz": 15
    },
    "19": {
        "id": 19,
        "nama": "Maryam",
        "arab": "مريم",
        "arti": "Maryam",
        "ayat": 98,
        "turun": "Makkiyah",
        "hal_mulai": 305,
        "hal_selesai": 312,
        "juz": 16
    },
    "20": {
        "id": 20,
        "nama": "Ta-Ha",
        "arab": "طه",
        "arti": "Ta-Ha",
        "ayat": 135,
        "turun": "Makkiyah",
        "hal_mulai": 312,
        "hal_selesai": 321,
        "juz": 16
    },
    "21": {
        "id": 21,
        "nama": "Al-Anbiya'",
        "arab": "الأنبياء",
        "arti": "Para Nabi",
        "ayat": 112,
        "turun": "Makkiyah",
        "hal_mulai": 322,
        "hal_selesai": 331,
        "juz": 17
    },
    "22": {
        "id": 22,
        "nama": "Al-Hajj",
        "arab": "الحج",
        "arti": "Haji",
        "ayat": 78,
        "turun": "Madaniyah",
        "hal_mulai": 332,
        "hal_selesai": 341,
        "juz": 17
    },
    "23": {
        "id": 23,
        "nama": "Al-Mu'minun",
        "arab": "المؤمنون",
        "arti": "Orang-Orang Mukmin",
        "ayat": 118,
        "turun": "Makkiyah",
        "hal_mulai": 342,
        "hal_selesai": 349,
        "juz": 18
    },
    "24": {
        "id": 24,
        "nama": "An-Nur",
        "arab": "النور",
        "arti": "Cahaya",
        "ayat": 64,
        "turun": "Madaniyah",
        "hal_mulai": 350,
        "hal_selesai": 359,
        "juz": 18
    },
    "25": {
        "id": 25,
        "nama": "Al-Furqan",
        "arab": "الفرقان",
        "arti": "Pembeda",
        "ayat": 77,
        "turun": "Makkiyah",
        "hal_mulai": 359,
        "hal_selesai": 366,
        "juz": 18
    },
    "26": {
        "id": 26,
        "nama": "Asy-Syu'ara'",
        "arab": "الشعراء",
        "arti": "Para Penyair",
        "ayat": 227,
        "turun": "Makkiyah",
        "hal_mulai": 367,
        "hal_selesai": 376,
        "juz": 19
    },
    "27": {
        "id": 27,
        "nama": "An-Naml",
        "arab": "النمل",
        "arti": "Semut",
        "ayat": 93,
        "turun": "Makkiyah",
        "hal_mulai": 377,
        "hal_selesai": 385,
        "juz": 19
    },
    "28": {
        "id": 28,
        "nama": "Al-Qashash",
        "arab": "القصص",
        "arti": "Kisah-Kisah",
        "ayat": 88,
        "turun": "Makkiyah",
        "hal_mulai": 385,
        "hal_selesai": 396,
        "juz": 20
    },
    "29": {
        "id": 29,
        "nama": "Al-'Ankabut",
        "arab": "العنكبوت",
        "arti": "Laba-Laba",
        "ayat": 69,
        "turun": "Makkiyah",
        "hal_mulai": 396,
        "hal_selesai": 404,
        "juz": 20
    },
    "30": {
        "id": 30,
        "nama": "Ar-Rum",
        "arab": "الروم",
        "arti": "Bangsa Romawi",
        "ayat": 60,
        "turun": "Makkiyah",
        "hal_mulai": 404,
        "hal_selesai": 410,
        "juz": 21
    },
    "31": {
        "id": 31,
        "nama": "Luqman",
        "arab": "لقمان",
        "arti": "Keluarga Luqman",
        "ayat": 34,
        "turun": "Makkiyah",
        "hal_mulai": 411,
        "hal_selesai": 415,
        "juz": 21
    },
    "32": {
        "id": 32,
        "nama": "As-Sajdah",
        "arab": "السجدة",
        "arti": "Sujud",
        "ayat": 30,
        "turun": "Makkiyah",
        "hal_mulai": 415,
        "hal_selesai": 417,
        "juz": 21
    },
    "33": {
        "id": 33,
        "nama": "Al-Ahzab",
        "arab": "الأحزاب",
        "arti": "Golongan yang Bersekutu",
        "ayat": 73,
        "turun": "Madaniyah",
        "hal_mulai": 418,
        "hal_selesai": 427,
        "juz": 21
    },
    "34": {
        "id": 34,
        "nama": "Saba'",
        "arab": "سبإ",
        "arti": "Kaum Saba",
        "ayat": 54,
        "turun": "Makkiyah",
        "hal_mulai": 428,
        "hal_selesai": 434,
        "juz": 22
    },
    "35": {
        "id": 35,
        "nama": "Fathir",
        "arab": "فاطر",
        "arti": "Pencipta",
        "ayat": 45,
        "turun": "Makkiyah",
        "hal_mulai": 434,
        "hal_selesai": 440,
        "juz": 22
    },
    "36": {
        "id": 36,
        "nama": "Ya-Sin",
        "arab": "يس",
        "arti": "Ya Sin",
        "ayat": 83,
        "turun": "Makkiyah",
        "hal_mulai": 440,
        "hal_selesai": 445,
        "juz": 22
    },
    "37": {
        "id": 37,
        "nama": "Ash-Shaffat",
        "arab": "الصافات",
        "arti": "Barisan-Barisan",
        "ayat": 182,
        "turun": "Makkiyah",
        "hal_mulai": 446,
        "hal_selesai": 452,
        "juz": 23
    },
    "38": {
        "id": 38,
        "nama": "Shad",
        "arab": "ص",
        "arti": "Shad",
        "ayat": 88,
        "turun": "Makkiyah",
        "hal_mulai": 453,
        "hal_selesai": 458,
        "juz": 23
    },
    "39": {
        "id": 39,
        "nama": "Az-Zumar",
        "arab": "الزمر",
        "arti": "Rombongan",
        "ayat": 75,
        "turun": "Makkiyah",
        "hal_mulai": 458,
        "hal_selesai": 467,
        "juz": 23
    },
    "40": {
        "id": 40,
        "nama": "Ghafir",
        "arab": "غافر",
        "arti": "Yang Mengampuni",
        "ayat": 85,
        "turun": "Makkiyah",
        "hal_mulai": 467,
        "hal_selesai": 476,
        "juz": 24
    },
    "41": {
        "id": 41,
        "nama": "Fushshilat",
        "arab": "فصلت",
        "arti": "Yang Dijelaskan",
        "ayat": 54,
        "turun": "Makkiyah",
        "hal_mulai": 477,
        "hal_selesai": 482,
        "juz": 24
    },
    "42": {
        "id": 42,
        "nama": "Asy-Syura",
        "arab": "الشورى",
        "arti": "Musyawarah",
        "ayat": 53,
        "turun": "Makkiyah",
        "hal_mulai": 483,
        "hal_selesai": 489,
        "juz": 25
    },
    "43": {
        "id": 43,
        "nama": "Az-Zukhruf",
        "arab": "الزخرف",
        "arti": "Perhiasan",
        "ayat": 89,
        "turun": "Makkiyah",
        "hal_mulai": 489,
        "hal_selesai": 495,
        "juz": 25
    },
    "44": {
        "id": 44,
        "nama": "Ad-Dukhan",
        "arab": "الدخان",
        "arti": "Kabut",
        "ayat": 59,
        "turun": "Makkiyah",
        "hal_mulai": 496,
        "hal_selesai": 498,
        "juz": 25
    },
    "45": {
        "id": 45,
        "nama": "Al-Jatsiyah",
        "arab": "الجاثية",
        "arti": "Yang Berlutut",
        "ayat": 37,
        "turun": "Makkiyah",
        "hal_mulai": 499,
        "hal_selesai": 502,
        "juz": 25
    },
    "46": {
        "id": 46,
        "nama": "Al-Ahqaf",
        "arab": "الأحقاف",
        "arti": "Bukit Pasir",
        "ayat": 35,
        "turun": "Makkiyah",
        "hal_mulai": 502,
        "hal_selesai": 506,
        "juz": 26
    },
    "47": {
        "id": 47,
        "nama": "Muhammad",
        "arab": "محمد",
        "arti": "Nabi Muhammad",
        "ayat": 38,
        "turun": "Madaniyah",
        "hal_mulai": 507,
        "hal_selesai": 510,
        "juz": 26
    },
    "48": {
        "id": 48,
        "nama": "Al-Fath",
        "arab": "الفتح",
        "arti": "Kemenangan",
        "ayat": 29,
        "turun": "Madaniyah",
        "hal_mulai": 511,
        "hal_selesai": 515,
        "juz": 26
    },
    "49": {
        "id": 49,
        "nama": "Al-Hujurat",
        "arab": "الحجرات",
        "arti": "Kamar-Kamar",
        "ayat": 18,
        "turun": "Madaniyah",
        "hal_mulai": 515,
        "hal_selesai": 517,
        "juz": 26
    },
    "50": {
        "id": 50,
        "nama": "Qaf",
        "arab": "ق",
        "arti": "Qaf",
        "ayat": 45,
        "turun": "Makkiyah",
        "hal_mulai": 518,
        "hal_selesai": 520,
        "juz": 26
    },
    "51": {
        "id": 51,
        "nama": "Adz-Dzariyat",
        "arab": "الذاريات",
        "arti": "Angin yang Menerbangkan",
        "ayat": 60,
        "turun": "Makkiyah",
        "hal_mulai": 520,
        "hal_selesai": 523,
        "juz": 26
    },
    "52": {
        "id": 52,
        "nama": "Ath-Thur",
        "arab": "الطور",
        "arti": "Bukit",
        "ayat": 49,
        "turun": "Makkiyah",
        "hal_mulai": 523,
        "hal_selesai": 525,
        "juz": 27
    },
    "53": {
        "id": 53,
        "nama": "An-Najm",
        "arab": "النجم",
        "arti": "Bintang",
        "ayat": 62,
        "turun": "Makkiyah",
        "hal_mulai": 526,
        "hal_selesai": 528,
        "juz": 27
    },
    "54": {
        "id": 54,
        "nama": "Al-Qamar",
        "arab": "القمر",
        "arti": "Bulan",
        "ayat": 55,
        "turun": "Makkiyah",
        "hal_mulai": 528,
        "hal_selesai": 531,
        "juz": 27
    },
    "55": {
        "id": 55,
        "nama": "Ar-Rahman",
        "arab": "الرحمن",
        "arti": "Yang Maha Pemurah",
        "ayat": 78,
        "turun": "Madaniyah",
        "hal_mulai": 531,
        "hal_selesai": 534,
        "juz": 27
    },
    "56": {
        "id": 56,
        "nama": "Al-Waqi'ah",
        "arab": "الواقعة",
        "arti": "Hari Kiamat",
        "ayat": 96,
        "turun": "Makkiyah",
        "hal_mulai": 534,
        "hal_selesai": 537,
        "juz": 27
    },
    "57": {
        "id": 57,
        "nama": "Al-Hadid",
        "arab": "الحديد",
        "arti": "Besi",
        "ayat": 29,
        "turun": "Madaniyah",
        "hal_mulai": 537,
        "hal_selesai": 541,
        "juz": 27
    },
    "58": {
        "id": 58,
        "nama": "Al-Mujadilah",
        "arab": "المجادلة",
        "arti": "Wanita yang Mengajukan Gugatan",
        "ayat": 22,
        "turun": "Madaniyah",
        "hal_mulai": 542,
        "hal_selesai": 545,
        "juz": 28
    },
    "59": {
        "id": 59,
        "nama": "Al-Hasyr",
        "arab": "الحشر",
        "arti": "Pengusiran",
        "ayat": 24,
        "turun": "Madaniyah",
        "hal_mulai": 545,
        "hal_selesai": 548,
        "juz": 28
    },
    "60": {
        "id": 60,
        "nama": "Al-Mumtahanah",
        "arab": "الممتحنة",
        "arti": "Perempuan yang Diuji",
        "ayat": 13,
        "turun": "Madaniyah",
        "hal_mulai": 549,
        "hal_selesai": 551,
        "juz": 28
    },
    "61": {
        "id": 61,
        "nama": "Ash-Shaff",
        "arab": "الصف",
        "arti": "Barisan",
        "ayat": 14,
        "turun": "Madaniyah",
        "hal_mulai": 551,
        "hal_selesai": 552,
        "juz": 28
    },
    "62": {
        "id": 62,
        "nama": "Al-Jumu'ah",
        "arab": "الجمعة",
        "arti": "Hari Jumat",
        "ayat": 11,
        "turun": "Madaniyah",
        "hal_mulai": 553,
        "hal_selesai": 554,
        "juz": 28
    },
    "63": {
        "id": 63,
        "nama": "Al-Munafiqun",
        "arab": "المنافقون",
        "arti": "Orang-Orang Munafik",
        "ayat": 11,
        "turun": "Madaniyah",
        "hal_mulai": 554,
        "hal_selesai": 555,
        "juz": 28
    },
    "64": {
        "id": 64,
        "nama": "At-Taghabun",
        "arab": "التغابن",
        "arti": "Hari Dinampakkan Kesalahan",
        "ayat": 18,
        "turun": "Madaniyah",
        "hal_mulai": 556,
        "hal_selesai": 557,
        "juz": 28
    },
    "65": {
        "id": 65,
        "nama": "Ath-Thalaq",
        "arab": "الطلاق",
        "arti": "Perceraian",
        "ayat": 12,
        "turun": "Madaniyah",
        "hal_mulai": 558,
        "hal_selesai": 559,
        "juz": 28
    },
    "66": {
        "id": 66,
        "nama": "At-Tahrim",
        "arab": "التحريم",
        "arti": "Pengharaman",
        "ayat": 12,
        "turun": "Madaniyah",
        "hal_mulai": 560,
        "hal_selesai": 561,
        "juz": 28
    },
    "67": {
        "id": 67,
        "nama": "Al-Mulk",
        "arab": "الملك",
        "arti": "Kerajaan",
        "ayat": 30,
        "turun": "Makkiyah",
        "hal_mulai": 562,
        "hal_selesai": 564,
        "juz": 29
    },
    "68": {
        "id": 68,
        "nama": "Al-Qalam",
        "arab": "القلم",
        "arti": "Pena",
        "ayat": 52,
        "turun": "Makkiyah",
        "hal_mulai": 564,
        "hal_selesai": 566,
        "juz": 29
    },
    "69": {
        "id": 69,
        "nama": "Al-Haqqah",
        "arab": "الحاقة",
        "arti": "Hari Kiamat",
        "ayat": 52,
        "turun": "Makkiyah",
        "hal_mulai": 566,
        "hal_selesai": 568,
        "juz": 29
    },
    "70": {
        "id": 70,
        "nama": "Al-Ma'arij",
        "arab": "المعارج",
        "arti": "Tempat Naik",
        "ayat": 44,
        "turun": "Makkiyah",
        "hal_mulai": 568,
        "hal_selesai": 570,
        "juz": 29
    },
    "71": {
        "id": 71,
        "nama": "Nuh",
        "arab": "نوح",
        "arti": "Nabi Nuh",
        "ayat": 28,
        "turun": "Makkiyah",
        "hal_mulai": 570,
        "hal_selesai": 571,
        "juz": 29
    },
    "72": {
        "id": 72,
        "nama": "Al-Jinn",
        "arab": "الجن",
        "arti": "Jin",
        "ayat": 28,
        "turun": "Makkiyah",
        "hal_mulai": 572,
        "hal_selesai": 573,
        "juz": 29
    },
    "73": {
        "id": 73,
        "nama": "Al-Muzzammil",
        "arab": "المزمل",
        "arti": "Orang yang Berselimut",
        "ayat": 20,
        "turun": "Makkiyah",
        "hal_mulai": 574,
        "hal_selesai": 575,
        "juz": 29
    },
    "74": {
        "id": 74,
        "nama": "Al-Muddaststir",
        "arab": "المدثر",
        "arti": "Orang yang Berkemul",
        "ayat": 56,
        "turun": "Makkiyah",
        "hal_mulai": 575,
        "hal_selesai": 577,
        "juz": 29
    },
    "75": {
        "id": 75,
        "nama": "Al-Qiyamah",
        "arab": "القيامة",
        "arti": "Hari Kiamat",
        "ayat": 40,
        "turun": "Makkiyah",
        "hal_mulai": 577,
        "hal_selesai": 578,
        "juz": 29
    },
    "76": {
        "id": 76,
        "nama": "Al-Insan",
        "arab": "الإنسان",
        "arti": "Manusia",
        "ayat": 31,
        "turun": "Madaniyah",
        "hal_mulai": 578,
        "hal_selesai": 580,
        "juz": 29
    },
    "77": {
        "id": 77,
        "nama": "Al-Mursalat",
        "arab": "المرسلات",
        "arti": "Malaikat yang Diutus",
        "ayat": 50,
        "turun": "Makkiyah",
        "hal_mulai": 580,
        "hal_selesai": 581,
        "juz": 29
    },
    "78": {
        "id": 78,
        "nama": "An-Naba'",
        "arab": "النبإ",
        "arti": "Berita Besar",
        "ayat": 40,
        "turun": "Makkiyah",
        "hal_mulai": 582,
        "hal_selesai": 583,
        "juz": 30
    },
    "79": {
        "id": 79,
        "nama": "An-Nazi'at",
        "arab": "النازعات",
        "arti": "Malaikat Pencabut",
        "ayat": 46,
        "turun": "Makkiyah",
        "hal_mulai": 583,
        "hal_selesai": 584,
        "juz": 30
    },
    "80": {
        "id": 80,
        "nama": "'Abasa",
        "arab": "عبس",
        "arti": "Ia Bermuka Masam",
        "ayat": 42,
        "turun": "Makkiyah",
        "hal_mulai": 585,
        "hal_selesai": 586,
        "juz": 30
    },
    "81": {
        "id": 81,
        "nama": "At-Takwir",
        "arab": "التكوير",
        "arti": "Menggulung",
        "ayat": 29,
        "turun": "Makkiyah",
        "hal_mulai": 586,
        "hal_selesai": 586,
        "juz": 30
    },
    "82": {
        "id": 82,
        "nama": "Al-Infithar",
        "arab": "الانفطار",
        "arti": "Terbelah",
        "ayat": 19,
        "turun": "Makkiyah",
        "hal_mulai": 587,
        "hal_selesai": 587,
        "juz": 30
    },
    "83": {
        "id": 83,
        "nama": "Al-Muthaffifin",
        "arab": "المطففين",
        "arti": "Orang-Orang Curang",
        "ayat": 36,
        "turun": "Makkiyah",
        "hal_mulai": 587,
        "hal_selesai": 589,
        "juz": 30
    },
    "84": {
        "id": 84,
        "nama": "Al-Insyiqaq",
        "arab": "الانشقاق",
        "arti": "Terbelah",
        "ayat": 25,
        "turun": "Makkiyah",
        "hal_mulai": 589,
        "hal_selesai": 590,
        "juz": 30
    },
    "85": {
        "id": 85,
        "nama": "Al-Buruj",
        "arab": "البروج",
        "arti": "Gugusan Bintang",
        "ayat": 22,
        "turun": "Makkiyah",
        "hal_mulai": 590,
        "hal_selesai": 590,
        "juz": 30
    },
    "86": {
        "id": 86,
        "nama": "Ath-Thariq",
        "arab": "الطارق",
        "arti": "Yang Datang di Malam Hari",
        "ayat": 17,
        "turun": "Makkiyah",
        "hal_mulai": 591,
        "hal_selesai": 591,
        "juz": 30
    },
    "87": {
        "id": 87,
        "nama": "Al-A'la",
        "arab": "الأعلى",
        "arti": "Yang Paling Tinggi",
        "ayat": 19,
        "turun": "Makkiyah",
        "hal_mulai": 591,
        "hal_selesai": 592,
        "juz": 30
    },
    "88": {
        "id": 88,
        "nama": "Al-Ghasyiyah",
        "arab": "الغاشية",
        "arti": "Hari Pembalasan",
        "ayat": 26,
        "turun": "Makkiyah",
        "hal_mulai": 592,
        "hal_selesai": 593,
        "juz": 30
    },
    "89": {
        "id": 89,
        "nama": "Al-Fajr",
        "arab": "الفجر",
        "arti": "Fajar",
        "ayat": 30,
        "turun": "Makkiyah",
        "hal_mulai": 593,
        "hal_selesai": 594,
        "juz": 30
    },
    "90": {
        "id": 90,
        "nama": "Al-Balad",
        "arab": "البلد",
        "arti": "Negeri",
        "ayat": 20,
        "turun": "Makkiyah",
        "hal_mulai": 594,
        "hal_selesai": 595,
        "juz": 30
    },
    "91": {
        "id": 91,
        "nama": "Asy-Syams",
        "arab": "الشمس",
        "arti": "Matahari",
        "ayat": 15,
        "turun": "Makkiyah",
        "hal_mulai": 595,
        "hal_selesai": 595,
        "juz": 30
    },
    "92": {
        "id": 92,
        "nama": "Al-Lail",
        "arab": "الليل",
        "arti": "Malam",
        "ayat": 21,
        "turun": "Makkiyah",
        "hal_mulai": 595,
        "hal_selesai": 596,
        "juz": 30
    },
    "93": {
        "id": 93,
        "nama": "Adh-Dhuha",
        "arab": "الضحى",
        "arti": "Waktu Dhuha",
        "ayat": 11,
        "turun": "Makkiyah",
        "hal_mulai": 596,
        "hal_selesai": 596,
        "juz": 30
    },
    "94": {
        "id": 94,
        "nama": "Asy-Syarh",
        "arab": "الشرح",
        "arti": "Melapangkan",
        "ayat": 8,
        "turun": "Makkiyah",
        "hal_mulai": 596,
        "hal_selesai": 596,
        "juz": 30
    },
    "95": {
        "id": 95,
        "nama": "At-Tin",
        "arab": "التين",
        "arti": "Buah Tin",
        "ayat": 8,
        "turun": "Makkiyah",
        "hal_mulai": 597,
        "hal_selesai": 597,
        "juz": 30
    },
    "96": {
        "id": 96,
        "nama": "Al-'Alaq",
        "arab": "العلق",
        "arti": "Segumpal Darah",
        "ayat": 19,
        "turun": "Makkiyah",
        "hal_mulai": 597,
        "hal_selesai": 598,
        "juz": 30
    },
    "97": {
        "id": 97,
        "nama": "Al-Qadr",
        "arab": "القدر",
        "arti": "Kemuliaan",
        "ayat": 5,
        "turun": "Makkiyah",
        "hal_mulai": 598,
        "hal_selesai": 598,
        "juz": 30
    },
    "98": {
        "id": 98,
        "nama": "Al-Bayyinah",
        "arab": "البينة",
        "arti": "Bukti yang Nyata",
        "ayat": 8,
        "turun": "Madaniyah",
        "hal_mulai": 598,
        "hal_selesai": 599,
        "juz": 30
    },
    "99": {
        "id": 99,
        "nama": "Az-Zalzalah",
        "arab": "الزلزلة",
        "arti": "Kegoncangan",
        "ayat": 8,
        "turun": "Madaniyah",
        "hal_mulai": 599,
        "hal_selesai": 599,
        "juz": 30
    },
    "100": {
        "id": 100,
        "nama": "Al-'Adiyat",
        "arab": "العاديات",
        "arti": "Kuda yang Berlari Kencang",
        "ayat": 11,
        "turun": "Makkiyah",
        "hal_mulai": 599,
        "hal_selesai": 600,
        "juz": 30
    },
    "101": {
        "id": 101,
        "nama": "Al-Qari'ah",
        "arab": "القارعة",
        "arti": "Hari Kiamat",
        "ayat": 11,
        "turun": "Makkiyah",
        "hal_mulai": 600,
        "hal_selesai": 600,
        "juz": 30
    },
    "102": {
        "id": 102,
        "nama": "At-Takatsur",
        "arab": "التكاثر",
        "arti": "Bermegah-Megahan",
        "ayat": 8,
        "turun": "Makkiyah",
        "hal_mulai": 600,
        "hal_selesai": 600,
        "juz": 30
    },
    "103": {
        "id": 103,
        "nama": "Al-'Ashr",
        "arab": "العصر",
        "arti": "Masa \/ Waktu",
        "ayat": 3,
        "turun": "Makkiyah",
        "hal_mulai": 601,
        "hal_selesai": 601,
        "juz": 30
    },
    "104": {
        "id": 104,
        "nama": "Al-Humazah",
        "arab": "الهمزة",
        "arti": "Pengumpat",
        "ayat": 9,
        "turun": "Makkiyah",
        "hal_mulai": 601,
        "hal_selesai": 601,
        "juz": 30
    },
    "105": {
        "id": 105,
        "nama": "Al-Fil",
        "arab": "الفيل",
        "arti": "Gajah",
        "ayat": 5,
        "turun": "Makkiyah",
        "hal_mulai": 601,
        "hal_selesai": 601,
        "juz": 30
    },
    "106": {
        "id": 106,
        "nama": "Quraisy",
        "arab": "قريش",
        "arti": "Suku Quraisy",
        "ayat": 4,
        "turun": "Makkiyah",
        "hal_mulai": 602,
        "hal_selesai": 602,
        "juz": 30
    },
    "107": {
        "id": 107,
        "nama": "Al-Ma'un",
        "arab": "الماعون",
        "arti": "Barang yang Berguna",
        "ayat": 7,
        "turun": "Makkiyah",
        "hal_mulai": 602,
        "hal_selesai": 602,
        "juz": 30
    },
    "108": {
        "id": 108,
        "nama": "Al-Kautsar",
        "arab": "الكوثر",
        "arti": "Nikmat yang Berlimpah",
        "ayat": 3,
        "turun": "Makkiyah",
        "hal_mulai": 602,
        "hal_selesai": 602,
        "juz": 30
    },
    "109": {
        "id": 109,
        "nama": "Al-Kafirun",
        "arab": "الكافرون",
        "arti": "Orang-Orang Kafir",
        "ayat": 6,
        "turun": "Makkiyah",
        "hal_mulai": 603,
        "hal_selesai": 603,
        "juz": 30
    },
    "110": {
        "id": 110,
        "nama": "An-Nashr",
        "arab": "النصر",
        "arti": "Pertolongan",
        "ayat": 3,
        "turun": "Madaniyah",
        "hal_mulai": 603,
        "hal_selesai": 603,
        "juz": 30
    },
    "111": {
        "id": 111,
        "nama": "Al-Lahab",
        "arab": "المسد",
        "arti": "Gejolak Api \/ Tali Sabut",
        "ayat": 5,
        "turun": "Makkiyah",
        "hal_mulai": 603,
        "hal_selesai": 603,
        "juz": 30
    },
    "112": {
        "id": 112,
        "nama": "Al-Ikhlas",
        "arab": "الإخلاص",
        "arti": "Memurnikan Keesaan Allah",
        "ayat": 4,
        "turun": "Makkiyah",
        "hal_mulai": 604,
        "hal_selesai": 604,
        "juz": 30
    },
    "113": {
        "id": 113,
        "nama": "Al-Falaq",
        "arab": "الفلق",
        "arti": "Waktu Subuh",
        "ayat": 5,
        "turun": "Makkiyah",
        "hal_mulai": 604,
        "hal_selesai": 604,
        "juz": 30
    },
    "114": {
        "id": 114,
        "nama": "An-Nas",
        "arab": "الناس",
        "arti": "Manusia",
        "ayat": 6,
        "turun": "Makkiyah",
        "hal_mulai": 604,
        "hal_selesai": 604,
        "juz": 30
    }
};

const QURAN_JUZS = {
    "1": {
        "juz": 1,
        "nama": "Alif Lam Mim",
        "surah_mulai": 1,
        "ayat_mulai": 1,
        "surah_selesai": 2,
        "ayat_selesai": 141,
        "hal_mulai": 1,
        "hal_selesai": 21
    },
    "2": {
        "juz": 2,
        "nama": "Sayaqulu",
        "surah_mulai": 2,
        "ayat_mulai": 142,
        "surah_selesai": 2,
        "ayat_selesai": 252,
        "hal_mulai": 22,
        "hal_selesai": 41
    },
    "3": {
        "juz": 3,
        "nama": "Tilkar Rusul",
        "surah_mulai": 2,
        "ayat_mulai": 253,
        "surah_selesai": 3,
        "ayat_selesai": 92,
        "hal_mulai": 42,
        "hal_selesai": 61
    },
    "4": {
        "juz": 4,
        "nama": "Lan Tanalu",
        "surah_mulai": 3,
        "ayat_mulai": 93,
        "surah_selesai": 4,
        "ayat_selesai": 23,
        "hal_mulai": 62,
        "hal_selesai": 81
    },
    "5": {
        "juz": 5,
        "nama": "Wal Muhshanatu",
        "surah_mulai": 4,
        "ayat_mulai": 24,
        "surah_selesai": 4,
        "ayat_selesai": 147,
        "hal_mulai": 82,
        "hal_selesai": 101
    },
    "6": {
        "juz": 6,
        "nama": "La Yuhibbullah",
        "surah_mulai": 4,
        "ayat_mulai": 148,
        "surah_selesai": 5,
        "ayat_selesai": 81,
        "hal_mulai": 102,
        "hal_selesai": 121
    },
    "7": {
        "juz": 7,
        "nama": "Wa Iza Samiu",
        "surah_mulai": 5,
        "ayat_mulai": 82,
        "surah_selesai": 6,
        "ayat_selesai": 110,
        "hal_mulai": 122,
        "hal_selesai": 141
    },
    "8": {
        "juz": 8,
        "nama": "Wa Lau Annana",
        "surah_mulai": 6,
        "ayat_mulai": 111,
        "surah_selesai": 7,
        "ayat_selesai": 87,
        "hal_mulai": 142,
        "hal_selesai": 161
    },
    "9": {
        "juz": 9,
        "nama": "Qalal Malao",
        "surah_mulai": 7,
        "ayat_mulai": 88,
        "surah_selesai": 8,
        "ayat_selesai": 40,
        "hal_mulai": 162,
        "hal_selesai": 181
    },
    "10": {
        "juz": 10,
        "nama": "Wa'lamu",
        "surah_mulai": 8,
        "ayat_mulai": 41,
        "surah_selesai": 9,
        "ayat_selesai": 92,
        "hal_mulai": 182,
        "hal_selesai": 201
    },
    "11": {
        "juz": 11,
        "nama": "Ya'tazirun",
        "surah_mulai": 9,
        "ayat_mulai": 93,
        "surah_selesai": 11,
        "ayat_selesai": 5,
        "hal_mulai": 202,
        "hal_selesai": 221
    },
    "12": {
        "juz": 12,
        "nama": "Wa Mamin Da'abbah",
        "surah_mulai": 11,
        "ayat_mulai": 6,
        "surah_selesai": 12,
        "ayat_selesai": 52,
        "hal_mulai": 222,
        "hal_selesai": 241
    },
    "13": {
        "juz": 13,
        "nama": "Wa Ma Ubarri'u",
        "surah_mulai": 12,
        "ayat_mulai": 53,
        "surah_selesai": 14,
        "ayat_selesai": 52,
        "hal_mulai": 242,
        "hal_selesai": 261
    },
    "14": {
        "juz": 14,
        "nama": "Rubama",
        "surah_mulai": 15,
        "ayat_mulai": 1,
        "surah_selesai": 16,
        "ayat_selesai": 128,
        "hal_mulai": 262,
        "hal_selesai": 281
    },
    "15": {
        "juz": 15,
        "nama": "Subhanallazi",
        "surah_mulai": 17,
        "ayat_mulai": 1,
        "surah_selesai": 18,
        "ayat_selesai": 74,
        "hal_mulai": 282,
        "hal_selesai": 301
    },
    "16": {
        "juz": 16,
        "nama": "Qala Alam",
        "surah_mulai": 18,
        "ayat_mulai": 75,
        "surah_selesai": 20,
        "ayat_selesai": 135,
        "hal_mulai": 302,
        "hal_selesai": 321
    },
    "17": {
        "juz": 17,
        "nama": "Iqtaraba",
        "surah_mulai": 21,
        "ayat_mulai": 1,
        "surah_selesai": 22,
        "ayat_selesai": 78,
        "hal_mulai": 322,
        "hal_selesai": 341
    },
    "18": {
        "juz": 18,
        "nama": "Qad Aflaha",
        "surah_mulai": 23,
        "ayat_mulai": 1,
        "surah_selesai": 25,
        "ayat_selesai": 20,
        "hal_mulai": 342,
        "hal_selesai": 361
    },
    "19": {
        "juz": 19,
        "nama": "Wa Qalal Lazina",
        "surah_mulai": 25,
        "ayat_mulai": 21,
        "surah_selesai": 27,
        "ayat_selesai": 55,
        "hal_mulai": 362,
        "hal_selesai": 381
    },
    "20": {
        "juz": 20,
        "nama": "A'man Khalaq",
        "surah_mulai": 27,
        "ayat_mulai": 56,
        "surah_selesai": 29,
        "ayat_selesai": 45,
        "hal_mulai": 382,
        "hal_selesai": 401
    },
    "21": {
        "juz": 21,
        "nama": "Utlu Ma Uhiya",
        "surah_mulai": 29,
        "ayat_mulai": 46,
        "surah_selesai": 33,
        "ayat_selesai": 30,
        "hal_mulai": 402,
        "hal_selesai": 421
    },
    "22": {
        "juz": 22,
        "nama": "Wa Man Yaqnut",
        "surah_mulai": 33,
        "ayat_mulai": 31,
        "surah_selesai": 36,
        "ayat_selesai": 27,
        "hal_mulai": 422,
        "hal_selesai": 441
    },
    "23": {
        "juz": 23,
        "nama": "Wa Maliya",
        "surah_mulai": 36,
        "ayat_mulai": 28,
        "surah_selesai": 39,
        "ayat_selesai": 31,
        "hal_mulai": 442,
        "hal_selesai": 461
    },
    "24": {
        "juz": 24,
        "nama": "Fa Man Azlamu",
        "surah_mulai": 39,
        "ayat_mulai": 32,
        "surah_selesai": 41,
        "ayat_selesai": 46,
        "hal_mulai": 462,
        "hal_selesai": 481
    },
    "25": {
        "juz": 25,
        "nama": "Ilaihi Yuraddu",
        "surah_mulai": 41,
        "ayat_mulai": 47,
        "surah_selesai": 45,
        "ayat_selesai": 37,
        "hal_mulai": 482,
        "hal_selesai": 501
    },
    "26": {
        "juz": 26,
        "nama": "Ha' Mim",
        "surah_mulai": 46,
        "ayat_mulai": 1,
        "surah_selesai": 51,
        "ayat_selesai": 30,
        "hal_mulai": 502,
        "hal_selesai": 521
    },
    "27": {
        "juz": 27,
        "nama": "Qala Fama Khatbukum",
        "surah_mulai": 51,
        "ayat_mulai": 31,
        "surah_selesai": 57,
        "ayat_selesai": 29,
        "hal_mulai": 522,
        "hal_selesai": 541
    },
    "28": {
        "juz": 28,
        "nama": "Qad Sami'allahu",
        "surah_mulai": 58,
        "ayat_mulai": 1,
        "surah_selesai": 66,
        "ayat_selesai": 12,
        "hal_mulai": 542,
        "hal_selesai": 561
    },
    "29": {
        "juz": 29,
        "nama": "Tabarakallazi",
        "surah_mulai": 67,
        "ayat_mulai": 1,
        "surah_selesai": 77,
        "ayat_selesai": 50,
        "hal_mulai": 562,
        "hal_selesai": 581
    },
    "30": {
        "juz": 30,
        "nama": "'Amma Yatasa'alun",
        "surah_mulai": 78,
        "ayat_mulai": 1,
        "surah_selesai": 114,
        "ayat_selesai": 6,
        "hal_mulai": 582,
        "hal_selesai": 604
    }
};

const QuranHelper = {
    getAllSurah: () => QURAN_SURAHS,
    getAllJuz: () => QURAN_JUZS,
    getSurah: (id) => QURAN_SURAHS[id] || null,
    
    getJuzByPage: (page) => {
        page = parseInt(page) || 1;
        if (page < 1) page = 1;
        if (page > 604) page = 604;
        for (const [juzNum, info] of Object.entries(QURAN_JUZS)) {
            if (page >= info.hal_mulai && page <= info.hal_selesai) {
                return parseInt(juzNum);
            }
        }
        return Math.min(30, Math.max(1, Math.ceil(page / 20.133)));
    },

    estimatePageBySurahAyah: (surahId, ayahNum) => {
        const s = QURAN_SURAHS[surahId];
        if (!s) return 1;
        ayahNum = parseInt(ayahNum) || 1;
        if (s.ayat <= 1 || s.hal_mulai === s.hal_selesai) return s.hal_mulai;
        const totalPagesInSurah = s.hal_selesai - s.hal_mulai + 1;
        const fraction = (ayahNum - 1) / Math.max(1, s.ayat - 1);
        const estimatedOffset = Math.floor(fraction * totalPagesInSurah);
        return Math.min(s.hal_selesai, s.hal_mulai + estimatedOffset);
    },

    calculatePageCount: (halMulai, halSelesai) => {
        halMulai = parseInt(halMulai) || 1;
        halSelesai = parseInt(halSelesai) || halMulai;
        return Math.max(0.5, halSelesai >= halMulai ? (halSelesai - halMulai + 1) : (halMulai - halSelesai + 1));
    },

    calculateNextTarget: (currentSurahId, currentAyah, arahHafalan = 'juz30_mundur', targetAyatCount = 10) => {
        currentSurahId = parseInt(currentSurahId) || 78;
        currentAyah = parseInt(currentAyah) || 0;
        const surah = QURAN_SURAHS[currentSurahId] || QURAN_SURAHS[78];
        const totalAyat = surah.ayat;

        if (arahHafalan === 'juz30_mundur') {
            if (currentAyah >= totalAyat || currentAyah === 0) {
                let nextSurahId = currentSurahId === 0 ? 78 : (currentAyah === 0 ? currentSurahId : currentSurahId + 1);
                if (nextSurahId > 114) nextSurahId = 114;
                const nextS = QURAN_SURAHS[nextSurahId];
                const nextAyatSelesai = Math.min(nextS.ayat, targetAyatCount);
                return {
                    next_surah_id: nextSurahId,
                    next_ayat_mulai: 1,
                    next_ayat_selesai: nextAyatSelesai,
                    surah_selesai_id: nextSurahId,
                    next_hal_mulai: nextS.hal_mulai,
                    next_hal_selesai: QuranHelper.estimatePageBySurahAyah(nextSurahId, nextAyatSelesai),
                    surah_nama: nextS.nama,
                    surah_arab: nextS.arab,
                    juz: nextS.juz
                };
            } else {
                const nextMulai = currentAyah + 1;
                const nextSelesai = Math.min(totalAyat, nextMulai + targetAyatCount - 1);
                return {
                    next_surah_id: currentSurahId,
                    next_ayat_mulai: nextMulai,
                    next_ayat_selesai: nextSelesai,
                    surah_selesai_id: currentSurahId,
                    next_hal_mulai: QuranHelper.estimatePageBySurahAyah(currentSurahId, nextMulai),
                    next_hal_selesai: QuranHelper.estimatePageBySurahAyah(currentSurahId, nextSelesai),
                    surah_nama: surah.nama,
                    surah_arab: surah.arab,
                    juz: surah.juz
                };
            }
        } else {
            // Juz 1 Maju
            if (currentAyah >= totalAyat || currentAyah === 0) {
                let nextSurahId = currentSurahId === 0 ? 1 : (currentAyah === 0 ? currentSurahId : currentSurahId + 1);
                if (nextSurahId > 114) nextSurahId = 114;
                const nextS = QURAN_SURAHS[nextSurahId];
                const nextAyatSelesai = Math.min(nextS.ayat, targetAyatCount);
                return {
                    next_surah_id: nextSurahId,
                    next_ayat_mulai: 1,
                    next_ayat_selesai: nextAyatSelesai,
                    surah_selesai_id: nextSurahId,
                    next_hal_mulai: nextS.hal_mulai,
                    next_hal_selesai: QuranHelper.estimatePageBySurahAyah(nextSurahId, nextAyatSelesai),
                    surah_nama: nextS.nama,
                    surah_arab: nextS.arab,
                    juz: nextS.juz
                };
            } else {
                const nextMulai = currentAyah + 1;
                const nextSelesai = Math.min(totalAyat, nextMulai + targetAyatCount - 1);
                return {
                    next_surah_id: currentSurahId,
                    next_ayat_mulai: nextMulai,
                    next_ayat_selesai: nextSelesai,
                    surah_selesai_id: currentSurahId,
                    next_hal_mulai: QuranHelper.estimatePageBySurahAyah(currentSurahId, nextMulai),
                    next_hal_selesai: QuranHelper.estimatePageBySurahAyah(currentSurahId, nextSelesai),
                    surah_nama: surah.nama,
                    surah_arab: surah.arab,
                    juz: surah.juz
                };
            }
        }
    }
};