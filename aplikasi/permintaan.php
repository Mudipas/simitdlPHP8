<?php
include('config.php');?>  
<script language="javascript">
function createRequestObject() {
var ajax;
if (navigator.appName == 'Microsoft Internet Explorer') {
ajax= new ActiveXObject('Msxml2.XMLHTTP');} 
else {
ajax = new XMLHttpRequest();}
return ajax;}

var http = createRequestObject();
function sendRequest(nomor){
if(nomor==""){
alert("Anda belum Memiliha Permintaan !");}
else{   
http.open('GET', 'aplikasi/filterpermintaan.php?nomor=' + encodeURIComponent(nomor) , true);
http.onreadystatechange = handleResponse;
http.send(null);}}

function handleResponse(){
if(http.readyState == 4){
var string = http.responseText.split('&&&');
document.getElementById('nomor').value = string[0];  
document.getElementById('status').value = string[1];
document.getElementById('ket').value = string[2];
document.getElementById('keterangan').value = string[3];
document.getElementById('qty').value = string[4];
document.getElementById('tgl').value = string[5];
document.getElementById('bagian').value = string[6];
document.getElementById('divisi').value = string[7];
document.getElementById('namabarang').value = string[8];
document.getElementById('nama').value = string[9];

 
                         
document.getElementById('jumlah').value="";
document.getElementById('jumlah').focus();

}}

var mywin; 
function popup(nomor){
	if(nomor==""){
alert("Anda belum memilih nomor permintaan");}
else{   
mywin=window.open("aplikasi/lap_permintaandetail.php?nomor=" + nomor ,"_blank",	"toolbar=yes,location=yes,menubar=yes,copyhistory=yes,directories=no,status=no,resizable=no,width=500, height=400"); mywin.moveTo(100,100);}
}


</script>
<?php
function kdauto($tabel, $inisial) {
    global $conn; // Pastikan koneksi sqlsrv tersedia

    // Ambil nama kolom pertama dan panjang maksimum kolom
  
    $query_struktur = "
    WITH ColumnInfo AS (
        SELECT 
            COLUMN_NAME,
            ROW_NUMBER() OVER (ORDER BY ORDINAL_POSITION) AS RowNum,
            CHARACTER_MAXIMUM_LENGTH  AS Columnlength
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_NAME = ?
    )
    SELECT 
        Columnlength AS TotalColumns,
        COLUMN_NAME AS SecondColumnName
    FROM ColumnInfo
    WHERE RowNum = 2;
    ";
    $params_struktur = array($tabel);
    $stmt_struktur = sqlsrv_query($conn, $query_struktur, $params_struktur);

    if ($stmt_struktur === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $field = null;
    $maxLength = null; // Default jika tidak ditemukan panjang kolom
    if ($row = sqlsrv_fetch_array($stmt_struktur, SQLSRV_FETCH_ASSOC)) {
        $field = $row['SecondColumnName']; // Ambil nama kolom pertama
        $maxLength = $row['TotalColumns'] ?? $maxLength;
    }
    sqlsrv_free_stmt($stmt_struktur);

    if ($field === null) {
        die("Kolom tidak ditemukan pada tabel: $tabel");
    }

    // Ambil nilai maksimum dari kolom tersebut
    $query_max = "SELECT MAX($field) AS maxKode FROM $tabel";
    $stmt_max = sqlsrv_query($conn, $query_max);

    if ($stmt_max === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($stmt_max, SQLSRV_FETCH_ASSOC);

    $angka = 0;
    if (!empty($row['maxKode'])) {
        $angka = (int) substr($row['maxKode'], strlen($inisial));
    }
    $angka++;

    sqlsrv_free_stmt($stmt_max);

    // Tentukan padding berdasarkan panjang kolom
    $padLength = $maxLength - strlen($inisial);
    if ($padLength <= 0) {
        die("Panjang padding tidak valid untuk kolom: $field");
    }

    // Menghasilkan kode baru
    return  $inisial. str_pad($angka, $padLength, "0", STR_PAD_LEFT); // Misalnya SUPP0001
}?>
            <div class="inner">
                <div class="row">
                    <div class="col-lg-12">


                        <h2> Data Permintaan Barang</h2>



                    </div>
                </div>

                <hr />


                <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
						     <button class="btn btn-primary" data-toggle="modal"  data-target="#newReg">
                                Tambah Permintaan 
                            </button>
                       
						</div>
                        <div class="panel-body">
                            <div class="table-responsive" style='overflow: scroll;'>
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
											  <th>Nama </th>
                                            <th>Bagian</th>
                                            <th>Divisi</th>
											<th>Nama Barang</th>
											<th>Jumlah</th>
											<th>Keterangan</th>
											<th>Status</th>
											<th>Masuk</th>
											<th>Keluar</th>
											<th>Sisa Order</th>
											<th>Detail</th>
											
											
											<th>Edit</th>
											<th>Hidden</th>
											<th>Nomor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                <?php 
				// $sql = mysql_query("SELECT * FROM permintaan where aktif='aktif' ");
				// if(mysql_num_rows($sql) > 0){
				// while($data = mysql_fetch_array($sql)){

				$query = "SELECT * FROM permintaan where aktif='aktif' ";
				$stmt = sqlsrv_query($conn, $query);
				
				// Periksa apakah query berhasil
				if ($stmt === false) {
					die(print_r(sqlsrv_errors(), true));
				}
				
				// Periksa apakah ada hasil
				if (sqlsrv_has_rows($stmt)) {
					while ($data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
				$nomor=$data['nomor'];
				$tgl=$data['tgl'] ? $data['tgl']->format('d/m/Y') : '';
				$nama=$data['nama'];
				$bagian=$data['bagian'];
				$divisi=$data['divisi'];
				$namabarang=$data['namabarang'];
				$qty=$data['qty'];
				//$masuk=$data['qtymasuk'];
				$keterangan=$data['keterangan'];
				$status=$data['status'];
				//$tgl2=$data['tgl2'];
				
	// $cek=mysql_query("select sum(qtymasuk) as totalmasuk,sum(qtykeluar) as totalkeluar from rincipermintaan where nomor='".$nomor."' ");
	//   while($result=mysql_fetch_array($cek)){
	//   $totalmasuk=$result['totalmasuk'];
	//   $totalkeluar=$result['totalkeluar'];
	
	//   }
		//echo $nomor;
	  	$sql = "SELECT SUM(qtymasuk) AS totalmasuk, SUM(qtykeluar) AS totalkeluar FROM rincipermintaan WHERE nomor = ?";
		$params = [$nomor]; // Gunakan prepared statement untuk keamanan
		$stmttotal = sqlsrv_query($conn, $sql, $params);

		if ($stmttotal === false) {
			die(print_r(sqlsrv_errors(), true)); // Debugging jika terjadi error
		}

		while ($result = sqlsrv_fetch_array($stmttotal, SQLSRV_FETCH_ASSOC)) {
			$totalmasuk = $result['totalmasuk'];
			$totalkeluar = $result['totalkeluar'];
		}
	  	$sisa=$qty-$totalmasuk; 
	    //$refresh = mysql_query("update permintaan set sisa='".$sisa."' where nomor='".$nomor."'  ");
		$sql = "UPDATE permintaan SET sisa = ? WHERE nomor = ?";
			$params = [$sisa, $nomor];

			// Eksekusi query menggunakan sqlsrv_query
			$stmtupdate = sqlsrv_query($conn, $sql, $params);
	  
				?>
				
                                        <tr class="gradeC">
                                            <td><?php echo $tgl ?></td>
											   <td><?php echo $nama ?></td>
                                            <td><?php echo $bagian ?></td>
                                            <td><?php echo $divisi ?></td>
											<td><?php echo $namabarang ?></td>
											<td><?php echo $qty?></td>
											<td><?php echo $keterangan ?></td>
											<td><?php echo $status ?></td>
											<td><?php echo $totalmasuk ?></td>
											<td><?php echo $totalkeluar ?></td>
											<td><?php echo $qty-$totalmasuk ?></td>
											<td align="center">
				<button class="btn btn-primary" value="<?php echo $nomor; ?>" onclick="popup(this.value)" name='tombol'>
                                Detail
                            </button>
		</td>
                             
											 <td class="center">
											 <!--<form action="user.php?menu=feditbarang" method="post" >
											<input type="hidden" name="idbarang" value=
											// />
										
											<button  name="tombol" class="btn text-muted text-center btn-primary" type="submit">Edit</button>
											</form>-->
											<button class="btn btn-primary" value='<?php echo $nomor; ?>' data-toggle="modal"  data-target="#newReggg" onclick="new sendRequest(this.value)">
                                Edit 
                            </button>
											
											</td>
											  <td class="center"><form action="aplikasi/deletepermintaan.php" method="post" >
											<input type="hidden" name="nomor" value=<?php echo $nomor; ?> />
										
											<button  name="tombol" class="btn text-muted text-center btn-danger" type="submit" onclick="return confirm('Apakah anda yakin akan menutup data ini?')">Hidden</button>
											</form> </td>
										 <td><? echo $nomor ?></td>
                                            
                                        </tr>
                <?php }}?>                      
                                    </tbody>
                                </table>
                            </div>
                           
                        </div>
                    </div>
                </div>
            </div>
     
	 
	   <div class="col-lg-12">
                        <div class="modal fade" id="newReg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title" id="H4"> Tambah Barang</h4>
                                        </div>
                                        <div class="modal-body">
                                       <form action="aplikasi/simpanpermintaan.php" method="post"  enctype="multipart/form-data" name="postform">
                                       <div class="form-group">
                                         
                                           
                                    
                                        </div>
										<div class="form-group">
Tanggal
                                            
                                          <input required='required' type="text" id="from" name="tgl" class="isi_tabel" onClick="if(self.gfPop)gfPop.fPopCalendar(document.postform.from);return false;"/><a href="javascript:void(0)" onClick="if(self.gfPop)gfPop.fPopCalendar(document.postform.from);return false;">
	  <img name="popcal" align="absmiddle" style="border:none" src="calender/calender.jpeg" width="34" height="29" border="0" alt=""></a>
                    
			               
				 </div>	
Nomor
				  <input class="form-control" type="text" name="nomor" value="<?php echo kdauto("permintaan",""); ?>" readonly>
				 <br>
				 <div class="form-group">
         Nama Peminta                                   
                                            <input  placeholder="Nama Peminta" class="form-control" type="text" name="nama" >
                                    
                                        </div>	
											
<div class="form-group">
                                           
		Bagian                                         
        <select class="form-control" name='bagian' required="required">
			<option></option>
			<?php
			// Query untuk mendapatkan data dari tabel bagian
			$sql = "SELECT * FROM bagian ORDER BY bagian ASC";
			$stmt = sqlsrv_query($conn, $sql);

			// Periksa jika ada kesalahan pada eksekusi query
			if ($stmt === false) {
				die(print_r(sqlsrv_errors(), true));
			}

			// Loop untuk mengambil setiap data dan menampilkan dalam tag <option>
			while ($datas = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
				$id_bagian = $datas['id_bagian'];
				$bagian = $datas['bagian'];
				?>
				<option value="<?php echo $bagian; ?>"><?php echo $bagian; ?></option>
				<?php
			}
			?>
		</select>

                                    
                                        </div>	
	
<div class="form-group">
                                           
Divisi
        <select class="form-control" name='divisi' required="required">
             <option ></option>
			 <option value="GARMENT" >GARMENT</option>
			 <option value="TEXTILE" >TEXTILE</option>
			  <option value="AMBASADOR" >AMBASADOR</option>
			  <option value="EFRATA" >EFRATA</option>
			  <option value="MAS" >MAS</option>
        </select>
                                    
                                        </div>	
	<div class="form-group">
         Nama Barang                                   
                                            <input  placeholder="Nama Barang" class="form-control" type="text" name="namabarang" >
                                    
                                        </div>	
										<div class="form-group">
         Jumlah                                   
                                            <input  placeholder="Jumlah" class="form-control" type="text" name="qty" >
                                    
                                        </div>	
										
																				
          Keterangan ( Alasan Permintaan )
 <textarea cols="45" rows="7" name="keterangan" class="form-control"  size="15px" placeholder="" ></textarea>                              
                                
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <button type="Submit" class="btn btn-danger" name='tombol'>Simpan</button>
                                        </div>
										<iframe width=174 height=189 name="gToday:normal:calender/agenda.js" id="gToday:normal:calender/agenda.js" src="calender/ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; top:-500px; left:-500px;">
</iframe>  
										    </form>
                       
						 </div>
                                </div>
                            </div>
                    </div>
					
					
					
					
					
	 <div class="col-lg-12">
                        <div class="modal fade" id="newReggg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title" id="H4"> Edit Status Permintaan</h4>
                                        </div>
								
										 <div class="modal-body">
								  <form action="aplikasi/updatepermintaan.php" method="post" >
                           <input   class="form-control" type="hidden" name="nomor" id="nomor" >
						   Jumlah Permintaan
						   <input   class="form-control" type="text" name="qty" id="qty" >
						
			Status				 
        <select class="form-control" name='status' id='status' required="required">
           
			 <option value="PENDING" >PENDING</option>
			 <option value="SELESAI" >SELESAI</option>
			
        </select><br>
		 <div class="form-group">
         Tanggal                                   
                                            <input   class="form-control" type="text" name="tgl" id="tgl"  >
                                    
                                        </div>	
		 <div class="form-group">
         Nama Peminta                                   
                                            <input  class="form-control" type="text" name="nama" id="nama" >
                                    
                                        </div>	
											
<div class="form-group">
                                           
Bagian                                         
		<select class="form-control" name='bagian' id='bagian' required="required">
			
			<?php
			// Query untuk mengambil data bagian
			$query = "SELECT * FROM bagian ORDER BY bagian ASC";
			$stmt = sqlsrv_query($conn, $query);

			if ($stmt === false) {
				// Handle error jika query gagal
				die(print_r(sqlsrv_errors(), true));
			}

			// Loop melalui hasil query dan buat opsi dropdown
			while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
				$id_bagian = $row['id_bagian'];
				$bagian = $row['bagian'];
				echo "<option value='$bagian'>$bagian</option>";
			}

			// Bebaskan statement resources
			sqlsrv_free_stmt($stmt);
			?>
		</select>

                                    
                                        </div>	
	
<div class="form-group">
                                           
Divisi
        <select class="form-control" name='divisi'   id='divisi'  required="required">
             <option ></option>
			 <option value="GARMENT" >GARMENT</option>
			 <option value="TEXTILE" >TEXTILE</option>
			  <option value="AMBASADOR" >AMBASADOR</option>
			  <option value="EFRATA" >EFRATA</option>
			  <option value="MAS" >MAS</option>
        </select>
                                    
                                        </div>	
	<div class="form-group">
         Nama Barang                                   
                                            <input  placeholder="Nama Barang" class="form-control" type="text" name="namabarang" id="namabarang" >
                                    
                                        </div>
		  Keterangan ( Alasan Permintaan )
 <textarea cols="45" rows="7" name="keterangan" class="form-control" id="keterangan" size="15px" placeholder="" ></textarea>                              
                                
		 Keterangan Setelah Proses  
 <textarea cols="45" rows="7" name="ket" id="ket" class="form-control" id="keterangan" size="15px" placeholder="" ></textarea>                              
               
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <button type="Submit" class="btn btn-danger" name='tombol'>Update</button>
                                        </div>
										    </form>
                                    </div>
                                </div>
                            </div>
                    </div>
					
					
					
					 