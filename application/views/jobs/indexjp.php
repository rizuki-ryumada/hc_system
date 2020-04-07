<!-- Begin Page Content -->
<div class="container-fluid">

	<!-- Page Heading -->
	<h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

	<div class="row">
		<div class="col-lg">
			<?= $this->session->flashdata('message'); ?>
		</div>
	</div>
	<div class="flash-jobs" data-flashdata="<?= $this->session->flashdata('flash'); ?>"></div>

	<div class="card shadow mb-2" id="print"> <!-- Profil Jabatan anda -->
		<!-- Card Header - Accordion -->
		<a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse" role="button">
			<h6 class="m-0 font-weight-bold text-black-50"><?= $posisi['position_name']?></h6>
		</a>
		<!-- Card Content - Collapse -->
		<div class="collapse show" id="collapseCardExample">
			<div class="card-body">
                <div class="row">
                    <div class="col-1 status-logo"> <!-- status logo -->
                        <div class="container d-flex h-100 m-0 p-0">
                            <div class="row justify-content-center align-self-center p-0 m-0">
                                <?php if($approval['status_approval'] == 0): ?>
                                    <i class="fa fa-exclamation-circle fa-3x" style="color: red"></i>
                                <?php elseif($approval['status_approval'] == 1): ?>
                                    <i class="fa fa-ellipsis-h fa-3x" style="color: gold"></i>
                                <?php elseif($approval['status_approval'] == 2): ?>
                                    <i class="fa fa-ellipsis-h fa-3x" style="color: gold"></i>
                                <?php elseif($approval['status_approval'] == 3): ?>
                                    <i class="fa fa-exclamation-triangle fa-3x" style="color: red"></i>
                                <?php elseif($approval['status_approval'] == 4): ?>
                                    <i class="fa fa-check-circle fa-3x" style="color: green"></i>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-10 status-text"> <!-- status text -->
                        <div class="row">
                            <div class="col-6">
                                <div class="row">
                                    <?php if(!empty($atasan[0]['position_name'])): //cek jika tidak punya atasan1?>
                                        <div class="col-3">Atasan 1</div><div class="col-1">:</div><div class="col-8"><?= $atasan[0]['position_name']; ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="row">
                                    <?php if(!empty($atasan[1]['position_name'])): //cek jika tidak punya atasan2?> 
                                        <div class="col-3">Atasan 2</div><div class="col-1">:</div><div class="col-8"><?= $atasan[1]['position_name']; ?></div>
                                    <?php endif; ?>
                                </div>

                                <!-- <div class="row">
                                    <div class="col-12"></div>
                                </div> -->
                            </div>
                            <div class="col-6">
                                    <!-- Status Approval Infomation
                                    0 = Belum diisi
                                    1 = Direview Atasan 1
                                    2 = Direview Atasan 2
                                    3 = Revisi
                                    4 = Selesai -->
                                <div class="row">
                                <!-- card status -->
                                    <div class="col-4">Status</div><div class="col-1">:</div><div class="col-7">
                                        <?php if($approval['status_approval'] == 0): ?>
                                            <span class="badge badge-danger">Belum disubmit</span>
                                        <?php elseif($approval['status_approval'] == 1): ?>
                                            <span class="badge badge-warning">Direview Atasan 1</span>
                                        <?php elseif($approval['status_approval'] == 2): ?>
                                            <span class="badge badge-warning">Direview Atasan 2</span>
                                        <?php elseif($approval['status_approval'] == 3): ?>
                                            <span class="badge badge-danger">Revisi</span>
                                        <?php elseif($approval['status_approval'] == 4): ?>
                                            <span class="badge badge-success">Selesai</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <?php if(!$approval['status_approval'] == 0): ?>
                                        <div class="col-4">Diperbarui</div><div class="col-1">:</div><div class="col-7"><?= date('d F Y, H:i', $approval['diperbarui']); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="row">
                                    <?php if($approval['pesan_revisi'] !== "null"): ?>
                                        <div class="col-4">Pesan</div><div class="col-1">:</div>
                                        <div class="col-7">
                                            <a tabindex="0" class="btn badge" role="button" data-toggle="popover" data-trigger="focus" data-placement="bottom" title="Pesan" data-content="<?= $approval['pesan_revisi']; ?>"><i class="fas fa-comment-dots text-info"></i></a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-1 status-action"> <!-- status action -->
                        <div class="container d-flex h-100 m-0 p-2"> <!-- this container make the element to vertically and horizontally centered -->
                            <div class="row justify-content-center align-self-center p-0 m-0">
                                <?php if($approval['is_edit'] == 1): ?>
                                    <a href="<?= base_url('jobs/myjp')?>"><i class="fa fa-pencil-alt fa-2x"></i></a>
                                <?php else: ?>
                                    <a href="<?= base_url('jobs/myjp')?>"><i class="fa fa-search fa-2x"></i></a>
                                 <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
            
            <!-- footer message -->
            <?php if($approval['status_approval'] == 0): ?>
                <div class="card-footer badge-danger">
	                Silakan isi, lengkapi, dan submit Job Profile Anda.
                </div>
            <?php elseif($approval['status_approval'] == 1): ?>
                <div class="card-footer badge-warning">
                    Job Profile sudah dikirim ke Atasan 1 anda, silakan tunggu hingga proses berikutnya.
                </div>
            <?php elseif($approval['status_approval'] == 2): ?>
                <div class="card-footer badge-warning">
                    Job Profile sudah dikirim ke Atasan 2 anda, silakan tunggu hingga proses berikutnya.
                </div>
            <?php elseif($approval['status_approval'] == 3): ?>
                <div class="card-footer badge-danger">
                    Anda diminta untuk merevisi job profle anda, klik tombol pesan untuk melihat revisi anda.
                </div>
            <?php elseif($approval['status_approval'] == 4): ?>
                <div class="card-footer badge-success">
                    Job Profile Anda sudah siap, selamat bekerja.
                </div>
            <?php endif; ?>
		</div>
	</div> <!-- /Profil Jabatan anda -->
    
    <div class="card shadow mb-2" id=""> <!-- My Task -->
		<!-- Card Header - Accordion -->
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-black-50">My Task</h6>
		</div>
		<!-- Card Content - Collapse -->
		<div class="collapse show">
        
			<div class="card-body">
				<div class="row mb-2">
					<table id="myTask" class="table table-striped table-hover"  style="display: table;width:100%">
                        <thead>
                            <th>Division</th>
                            <th>Departement</th>
                            <th>Position</th>
                            <th>Employee Name</th>
                            <th>Date</th>
                            <th style="min-width: 60px;"></th>
                        </thead>
                        <tbody>
                            <?php foreach($my_task as $v): ?>
                                <tr id="myTask-list">
                                    <td><?= $v['divisi'] ?></td>
                                    <td><?= $v['departement'] ?></td>
                                    <td><?= $v['posisi'] ?></td>
                                    <td><?= $v['name'] ?></td>
                                    <td><?= date('d F Y, H:i', $v['diperbarui']); ?></td>
                                    <td>
                                        <div class="container d-flex h-100 m-0 px-auto"> <!-- this container make the element to vertically and horizontally centered -->
                                            <div class="row justify-content-center align-self-center w-100 m-0">
                                                <a id="myTask-button" style="display: none;" href="<?= base_url('jobs/taskJp'); ?>?task=<?= $v['nik']; ?>&status=<?= $v['status_approval'] ?>"><i class="fa fa-search mx-auto"></i></a>    
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
				</div>

			</div>
			<!-- <div class="card-footer">
				This Is Footer
			</div> -->
		</div>
	</div> <!-- /My Task -->
</div>
<!-- /.container-fluid -->

</div>
<!-- End of Main Content -->