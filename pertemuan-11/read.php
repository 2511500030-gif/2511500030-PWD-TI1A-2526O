            <?php
            require 'koneksi.php';

            $sql = "SELECT * FROM tbl_tamu ORDER BY cid DESC";
            $q = mysqli_query($conn, $sql);

            if (!$q) {
                die("Query error: " . mysqli_error($conn));
            }

                

            $no = 1;
            ?>
            <table border="1" cellpadding="8" cellspacing="0">
            <tr>
            <th>No</th>
            <th>ID</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Pesan</th>
            <th>Created At</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($q)): ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= $row['cid']; ?></td>
                <td><?= htmlspecialchars($row['cnama']); ?></td>
                <td><?= htmlspecialchars($row['cemail']); ?></td>
                <td><?= nl2br(htmlspecialchars($row['cpesan'])); ?></td>

                
                <td>
                <?= isset($row['created_at']) ? $row['created_at'] : '—'; ?>
                </td>
            </tr>
            <?php endwhile; ?>
            </table>