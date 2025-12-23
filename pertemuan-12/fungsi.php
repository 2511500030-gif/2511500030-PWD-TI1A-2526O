 <?php
            function redirect_ke($url)
            {
            header ("location: " . $url);
            exit();
            }

            function tidakKosong($str)
            {
            return strlen(trim($str)) > 0;
            }

            function formatTanggal($tgl)
            {
            return date("d M Y", strtotime($tgl));
            }
            function bersihkan($str)
            {
            return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
            }


            function tampilkanBiodata($conf, $arr)
            {
            $html = "";
            foreach ($conf as $k => $v) {
                $label = $v["label"];
                $nilai = bersihkan($arr[$k] ?? '');
                $suffix = $v["suffix"];

                $html .= "<p><strong>{$label}</strong> {$nilai}{$suffix}</p>";
            }
            return $html;
            }