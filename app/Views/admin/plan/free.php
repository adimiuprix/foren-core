<?= $this->extend('admin/layout') ?>
<?= $this->section('admsec') ?>
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="col-md-12">
            <div class="card mb-6">
                <h5 class="card-header">Edit free plan</h5>
                <form action="<?= site_url('admin/plan/freeplan'); ?>" method="post">
                    <?= csrf_field(); ?>
                    <div class="card-body demo-vertical-spacing demo-only-element">
                        <small class="text-light fw-medium d-block">Plan name</small>
                        <div class="input-group">
                            <input type="text" class="form-control" name="plan_name" value="<?= $plan->plan_name;?>" placeholder="Plan name" required />
                        </div>
                        <small class="text-light fw-medium d-block">Earning (day)</small>
                        <div class="input-group">
                            <input type="number" class="form-control" name="earn_day" value="<?= $plan->earning_per_day;?>" placeholder="Earning per day" required />
                        </div>
                        <small class="text-light fw-medium d-block">Earning rate</small>
                        <div class="input-group">
                            <input type="number" class="form-control" name="earn_rate" value="<?= $plan->earning_rate;?>" placeholder="Earning rate" required />
                        </div>
                        <small class="text-light fw-medium d-block">Profit</small>
                        <div class="input-group">
                            <input type="number" class="form-control" name="plan_profit" value="<?= $plan->profit;?>" placeholder="Profit" required />
                        </div>
                    </div>
                    <div class="d-grid col-lg-6 mx-auto mb-5">
                        <button type="submit" class="btn btn-primary btn-lg waves-effect waves-light">Edit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- / Content -->

    <?= $this->include('admin/partialls/footer');?>
</div>
<?= $this->endSection() ?>