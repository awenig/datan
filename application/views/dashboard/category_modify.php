

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark"><?= $title ?></h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
                <?= form_open_multipart('admin/categories/modify/'.$category['id']); ?>
                  <div class="form-group">
                    <label>Nom</label>
                    <input type="text" class="form-control" autocomplete="off" name="name" value="<?= $category['name'] ?>">
                  </div>
                  <div class="form-group">
                    <label>Slug</label>
                    <input type="text" class="form-control" autocomplete="off" name="slug" value="<?= $category['slug'] ?>">
                  </div>
                  <?php if ($this->session->userdata('type') == 'admin'): ?>
                    <div class="form-group">
                      <div class="form-check">
                        <?php if ($category['state'] == "draft"): ?>
                          <input class="form-check-input" type="radio" name="state" value="draft" checked="">
                          <?php else: ?>
                            <input class="form-check-input" type="radio" name="state" value="draft">
                        <?php endif; ?>
                        <label class="form-check-label">Draft</label>
                      </div>
                      <div class="form-check">
                        <?php if ($category['state'] == "published"): ?>
                          <input class="form-check-input" type="radio" name="state" value="published" checked="">
                          <?php else: ?>
                          <input class="form-check-input" type="radio" name="state" value="published">
                        <?php endif; ?>
                        <label class="form-check-label">Published</label>
                      </div>
                    </div>
                  <?php endif; ?>
                  <button type="submit" class="btn btn-primary">Submit</button>
              </form>
              </div>
            </div>
          </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
    <div class="p-3">
      <h5>Title</h5>
      <p>Sidebar content</p>
    </div>
  </aside>
  <!-- /.control-sidebar -->
