<?php
declare(strict_types=1);

namespace App;

use PDO;

class QuranSeeds
{
    public static function seedInitialSurahs(PDO $db): void
    {
        $count = $db->query("SELECT COUNT(*) FROM quran_teks WHERE surah_id = 1")->fetchColumn();
        if ($count > 0) {
            return;
        }

        $stmt = $db->prepare("
            INSERT OR IGNORE INTO quran_teks 
            (surah_id, nomor_ayat, teks_arab, teks_latin, terjemahan, juz, halaman) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        // Surah 1: Al-Fatihah (7 Ayat)
        $fatihah = [
            [1, 1, 'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ', 'Bismillaahir-rahmaanir-rahiim', 'Dengan nama Allah Yang Maha Pengasih, Maha Penyayang.', 1, 1],
            [1, 2, 'الْحَمْدُ لِلَّهِ رَبِّ الْعَالَمِينَ', "Al-hamdu lillaahi rabbil-'aalamiin", 'Segala puji bagi Allah, Tuhan seluruh alam,', 1, 1],
            [1, 3, 'الرَّحْمَٰنِ الرَّحِيمِ', 'Ar-rahmaanir-rahiim', 'Yang Maha Pengasih, Maha Penyayang,', 1, 1],
            [1, 4, 'مَالِكِ يَوْمِ الدِّينِ', 'Maaliki yawmid-diin', 'Pemilik hari pembalasan.', 1, 1],
            [1, 5, 'إِيَّاكَ نَعْبُدُ وَإِيَّاكَ نَسْتَعِينُ', 'Iyyaaka na\'budu wa iyyaaka nasta\'iin', 'Hanya kepada Engkaulah kami menyembah dan hanya kepada Engkaulah kami memohon pertolongan.', 1, 1],
            [1, 6, 'اهْدِنَا الصِّرَاطَ الْمُسْتَقِيمَ', 'Ihdinash-shiraathal-mustaqiim', 'Tunjukilah kami jalan yang lurus,', 1, 1],
            [1, 7, 'صِرَاطَ الَّذِينَ أَنْعَمْتَ عَلَيْهِمْ غَيْرِ الْمَغْضُوبِ عَلَيْهِمْ وَلَا الضَّالِّينَ', 'Shiraathalladziina an\'amta \'alaihim ghairil-maghdhuubi \'alaihim waladh-dhaalliin', '(yaitu) jalan orang-orang yang telah Engkau beri nikmat kepadanya; bukan (jalan) mereka yang dimurkai, dan bukan (pula jalan) mereka yang sesat.', 1, 1],
        ];

        foreach ($fatihah as $row) {
            $stmt->execute($row);
        }

        // Surah 78: An-Naba' (Ayat 1-10)
        $naba = [
            [78, 1, 'عَمَّ يَتَسَاءَلُونَ', "'Amma yatasaa-aluun", 'Tentang apakah mereka saling bertanya-tanya?', 30, 582],
            [78, 2, 'عَنِ النَّبَإِ الْعَظِيمِ', "'Anin-naba-il-'azhiim", 'Tentang berita yang besar (hari berbangkit),', 30, 582],
            [78, 3, 'الَّذِي هُمْ فِيهِ مُخْتَلِفُونَ', "Alladzii hum fiihi mukhtalifuun", 'yang dalam hal itu mereka berselisih.', 30, 582],
            [78, 4, 'كَلَّا سَيَعْلَمُونَ', "Kallaa saya'lamuun", 'Tidak! Kelak mereka akan mengetahui,', 30, 582],
            [78, 5, 'ثُمَّ كَلَّا سَيَعْلَمُونَ', "Tsumma kallaa saya'lamuun", 'sekali lagi tidak! Kelak mereka akan mengetahui.', 30, 582],
            [78, 6, 'أَلَمْ نَجْعَلِ الْأَرْضَ مِهَادًا', "Alam naj'alil-ardha mihaadaa", 'Bukankah Kami telah menjadikan bumi sebagai hamparan,', 30, 582],
            [78, 7, 'وَالْجِبَالَ أَوْتَادًا', "Wal-jibaala awtaadaa", 'dan gunung-gunung sebagai pasak?', 30, 582],
            [78, 8, 'وَخَلَقْنَاكُمْ أَزْوَاجًا', "Wa khalaqnaakum azwaajaa", 'Dan Kami menciptakan kamu berpasang-pasangan,', 30, 582],
            [78, 9, 'وَجَعَلْنَا نَوْمَكُمْ سُبَاتًا', "Wa ja'alnaa nawmakum subaataa", 'dan Kami menjadikan tidurmu untuk istirahat,', 30, 582],
            [78, 10, 'وَجَعَلْنَا اللَّيْلَ لِبَاسًا', "Wa ja'alnal-laila libaasaa", 'dan Kami menjadikan malam sebagai pakaian,', 30, 582],
            [78, 11, 'وَجَعَلْنَا النَّهَارَ مَعَاشًا', "Wa ja'alnan-nahaara ma'aasyaa", 'dan Kami menjadikan siang untuk mencari penghidupan,', 30, 582],
            [78, 12, 'وَبَنَيْنَا فَوْقَكُمْ سَبْعًا شِدَادًا', "Wa banainaa fawqakum sab'an syidaadaa", 'dan Kami membangun di atas kamu tujuh (langit) yang kokoh,', 30, 582],
            [78, 13, 'وَجَعَلْنَا سِرَاجًا وَهَّاجًا', "Wa ja'alnaa siraajan wahhaajaa", 'dan Kami menjadikan pelita yang terang-benderang (matahari),', 30, 582],
            [78, 14, 'وَأَنْزَلْنَا مِنَ الْمُعْصِرَاتِ مَاءً ثَجَّاجًا', "Wa anzalnaa minal-mu'shiraati maa-an tsajjaajaa", 'dan Kami turunkan dari awan air hujan yang tercurah dengan lebat,', 30, 582],
            [78, 15, 'لِنُخْرِجَ بِهِ حَبًّا وَنَبَاتًا', "Linukhrija bihii habban wa nabaataa", 'untuk Kami tumbuhkan dengan air itu biji-bijian dan tanam-tanaman,', 30, 582],
            [78, 16, 'وَجَنَّاتٍ أَلْفَافًا', "Wa jannaatin alfaafaa", 'dan kebun-kebun yang lebat?', 30, 582],
        ];

        foreach ($naba as $row) {
            $stmt->execute($row);
        }

        // Surah 112: Al-Ikhlas
        $ikhlas = [
            [112, 1, 'قُلْ هُوَ اللَّهُ أَحَدٌ', "Qul huwalloohu ahad", 'Katakanlah (Muhammad), "Dialah Allah, Yang Maha Esa."', 30, 604],
            [112, 2, 'اللَّهُ الصَّمَدُ', "Alloohush-shamad", 'Allah tempat meminta segala sesuatu.', 30, 604],
            [112, 3, 'لَمْ يَلِدْ وَلَمْ يُولَدْ', "Lam yalid wa lam yuulad", '(Allah) tidak beranak dan tidak pula diperanakkan,', 30, 604],
            [112, 4, 'وَلَمْ يَكُنْ لَهُ كُفُوًا أَحَدٌ', "Wa lam yakul lahuu kufuwan ahad", 'dan tidak ada sesuatu yang setara dengan Dia.', 30, 604],
        ];
        foreach ($ikhlas as $row) {
            $stmt->execute($row);
        }

        // Surah 113: Al-Falaq
        $falaq = [
            [113, 1, 'قُلْ أَعُوذُ بِرَبِّ الْفَلَقِ', "Qul a'uudzu birobbil-falaq", 'Katakanlah, "Aku berlindung kepada Tuhan yang menguasai subuh (fajar),', 30, 604],
            [113, 2, 'مِنْ شَرِّ مَا خَلَقَ', "Min syarri maa kholaq", 'dari kejahatan (makhluk yang) Dia ciptakan,', 30, 604],
            [113, 3, 'وَمِنْ شَرِّ غَاسِقٍ إِذَا وَقَبَ', "Wa min syarri ghaasiqin idzaa waqob", 'dan dari kejahatan malam apabila telah gelap gulita,', 30, 604],
            [113, 4, 'وَمِنْ شَرِّ النَّفَّاثَاتِ فِي الْعُقَدِ', "Wa min syarrin-naffaatsaati fil-'uqad", 'dan dari kejahatan (perempuan-perempuan) penyihir yang meniup pada buhul-buhul (talinya),', 30, 604],
            [113, 5, 'وَمِنْ شَرِّ حَاسِدٍ إِذَا حَسَدَ', "Wa min syarri haasidin idzaa hasad", 'dan dari kejahatan orang yang dengki apabila dia dengki."', 30, 604],
        ];
        foreach ($falaq as $row) {
            $stmt->execute($row);
        }

        // Surah 114: An-Nas
        $nas = [
            [114, 1, 'قُلْ أَعُوذُ بِرَبِّ النَّاسِ', "Qul a'uudzu birobbin-naas", 'Katakanlah, "Aku berlindung kepada Tuhannya manusia,', 30, 604],
            [114, 2, 'مَلِكِ النَّاسِ', "Malikin-naas", 'Raja manusia,', 30, 604],
            [114, 3, 'إِلَٰهِ النَّاسِ', "Ilaahin-naas", 'sembahan manusia,', 30, 604],
            [114, 4, 'مِنْ شَرِّ الْوَسْوَاسِ الْخَنَّاسِ', "Min syarril-waswaasil-khon-naas", 'dari kejahatan (bisikan) setan yang bersembunyi,', 30, 604],
            [114, 5, 'الَّذِي يُوَسْوِسُ فِي صُدُورِ النَّاسِ', "Alladzii yuwaswisu fii shuduurin-naas", 'yang membisikkan (kejahatan) ke dalam dada manusia,', 30, 604],
            [114, 6, 'مِنَ الْجِنَّةِ وَالنَّاسِ', "Minal-jinnati wan-naas", 'dari (golongan) jin dan manusia."', 30, 604],
        ];
        foreach ($nas as $row) {
            $stmt->execute($row);
        }
    }
}

