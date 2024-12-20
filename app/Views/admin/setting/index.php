<?= $this->extend('admin/layout') ?>
<?= $this->section('admsec') ?>
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="col-md-12">
            <div class="card mb-6">
                <h5 class="card-header">Setting</h5>
                <form action="" method="post">
                    <div class="card-body demo-vertical-spacing demo-only-element">
                        <small class="text-light fw-medium d-block">Site name</small>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Type site name">
                        </div>
                        <small class="text-light fw-medium d-block">Currency name</small>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Type currency name">
                        </div>
                        <small class="text-light fw-medium d-block">Currency code</small>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Type currency code">
                        </div>
                        <div class="d-grid col-lg-6 mx-auto pt-5">
                            <button type="submit" class="btn btn-primary btn-lg waves-effect waves-light">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- / Content -->
    <?= $this->include('admin/partialls/footer');?>
</div>
<?= $this->endSection() ?>