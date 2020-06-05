@extends('layouts.home')

@section('title')
PrediksiKu
@endsection

@section('perhitungan')
active
@endsection

@section('content')
  <div class="main-content container-fluid">
    <div class="row">
      <div class="col-xs-12">
        <div class="panel panel-default">
          <div class="panel-heading panel-heading-divider">Introduction
            <span class="panel-subtitle">
              Program sederhana untuk melakukan prediksi pada suatu data. Program ini menggunakan Fuzzy Time Series Model Chen & Lee. Siapkan data Time Series (runtutan waktu) dari Microsoft Excel anda dengan menggunakan format .xlsx untuk melakukan analisis data anda.
            </span>
            <h4>
              Panduan :
            </h4>
            <span class="panel-subtitle">
                <li>Import Data menggunakan format file yang telah ditentukan (.xlsx) dengan cara "Choose File" lalu klik "Import"<div class=""></div></li>
                <li>Jika Data anda berhasil tampil, klik "Hitung Data" dan tentukan Jumlah Orde yang anda inginkan.</li>
                <li>Apabila anda ingin merubah data, klik "Reset Data" untuk menghapus seluruh data anda, lalu lakukan Import Ulang dengan data terbaru anda.</li>
                <li>Klik Unduh Template, untuk mengunduh template data agar sesuai dengan format database pada sistem ini.</li>
              </span>
          
          </div>
          <div class="panel-body">
            <div class="row">
              @if (session()->has('warning'))
              <div role="alert" class="alert alert-contrast alert-warning alert-dismissible">
                  <div class="icon"><span class="mdi mdi-alert-triangle"></span></div>
                  <div class="message">
                    <button type="button" data-dismiss="alert" aria-label="Close" class="close"><span aria-hidden="true" class="mdi mdi-close"></span></button><strong>Warning!</strong> {{Session::get('warning')}}
                  </div>
              </div>
              @endif
              @if (session()->has('success'))
              <div role="alert" class="alert alert-contrast alert-success alert-dismissible">
                <div class="icon"><span class="mdi mdi-check"></span></div>
                <div class="message">
                  <button type="button" data-dismiss="alert" aria-label="Close" class="close"><span aria-hidden="true" class="mdi mdi-close"></span></button><strong>Good!</strong> {{Session::get('success')}}
                </div>
              </div>
              @endif
              @if (session()->has('danger'))
              <div role="alert" class="alert alert-contrast alert-danger alert-dismissible">
                <div class="icon"><span class="mdi mdi-check"></span></div>
                <div class="message">
                  <button type="button" data-dismiss="alert" aria-label="Close" class="close"><span aria-hidden="true" class="mdi mdi-close"></span></button><strong>Deleted!</strong> {{Session::get('danger')}}
                </div>
              </div>
              @endif
            </div>

            <div class="row">
              <div class="col-xs-12">
                <h4>Import Data :</h4>
              </div>
              <div class="col-xs-12">
                <form name="tambah" id="tambah" action="{{route('dataset.import')}}" method="POST" enctype="multipart/form-data">
                  {{csrf_field()}}
                  <input type="hidden" name="_method" value="POST">
                    <div class="form-group file-field input-field">
                      <div class="btn">
                        <input name="excel" type="file">
                      </div>
                      <button class="btn btn-primary" type="submit">Import</button>
                      <a class="btn btn-warning" href="{{asset('storage/file/template_dataset_sembuh.xlsx')}}">Unduh Template</a>
                    </div>
                </form>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-8">
              </div>
              <div class="col-xs-4">
                  <button data-modal="full-success" class="btn btn-space btn-success md-trigger">Hitung Data</button>
                  <button data-modal="full-danger" class="btn btn-space btn-danger md-trigger">Reset Data</button>
              </div>
            </div>

            <div class="row">
              <table id="table1" class="table table-striped table-hover table-fw-widget">
                <thead>
                  <tr>
                    <th>Tanggal</th>
                    <th>Data</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($dataset as $key)
                    <tr>
                      <td class="center">{{$key->tanggal}}</td>
                      <td class="center">{{$key->data}}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="full-success" class="modal-container modal-full-color modal-full-color-success modal-effect-8">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
      </div>
      <div class="modal-body">
        <div class="text-center"><span class="modal-main-icon mdi mdi-assignment"></span>
          <h3>Mulai perhitungan!</h3>
          <p>Masukkan jumlah orde yang diinginkan.</p>
          <form class="" action="{{route('dataset.hitung')}}" method="post">
            @csrf
            <div class="md-form mb-5">
              <input type="number" id="orde" name="orde" class="form-control" required>
            </div>
          <div class="xs-mt-50">

            <button type="button" data-dismiss="modal" class="btn btn-default btn-space modal-close">Batal</button>
            <button type="submit" class="btn btn-success btn-space">Hitung!</button>
          </div>
          </form>
        </div>
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
  <div id="full-danger" class="modal-container modal-full-color modal-full-color-danger modal-effect-8">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" data-dismiss="modal" aria-hidden="true" class="close modal-close"><span class="mdi mdi-close"></span></button>
      </div>
      <div class="modal-body">
        <div class="text-center"><span class="modal-main-icon mdi mdi-alert-octagon"></span>
          <h3>Reset Data!</h3>
          <h4>Anda yakin ingin melakukan Reset Data?</h4>
          <div class="xs-mt-50">
            <button type="button" data-dismiss="modal" class="btn btn-default btn-space modal-close">Batal</button>
            <a href="{{route('dataset.reset')}}" class="btn btn-success btn-space">Reset</a>
          </div>
        </div>
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>


@endsection

@section('js')
<script src="{{asset('lib/datatables/js/jquery.dataTables.min.js')}}" type="text/javascript"></script>
<script src="{{asset('lib/datatables/js/dataTables.bootstrap.min.js')}}" type="text/javascript"></script>
<script src="{{asset('lib/jquery.niftymodals/dist/jquery.niftymodals.js')}}" type="text/javascript"></script>

<script type="text/javascript">
$.fn.niftyModal('setDefaults',{
      	overlaySelector: '.modal-overlay',
      	closeSelector: '.modal-close',
      	classAddAfterOpen: 'modal-show',
      });
     //We use this to apply style to certain elements
    $.extend( true, $.fn.dataTable.defaults, {
      dom:
        "<'row be-datatable-header'<'col-sm-6'l><'col-sm-6'f>>" +
        "<'row be-datatable-body'<'col-sm-12'tr>>" +
        "<'row be-datatable-footer'<'col-sm-5'i><'col-sm-7'p>>"
    } );

    $("#table1").dataTable();

    //Remove search & paging dropdown
    $("#table2").dataTable({
      pageLength: 6,
      dom:  "<'row be-datatable-body'<'col-sm-12'tr>>" +
            "<'row be-datatable-footer'<'col-sm-5'i><'col-sm-7'p>>"
    });

    //Enable toolbar button functions
    $("#table3").dataTable({
      buttons: [
        'copy', 'excel', 'pdf', 'print'
      ],
      "lengthMenu": [[6, 10, 25, 50, -1], [6, 10, 25, 50, "All"]],
      dom:  "<'row be-datatable-header'<'col-sm-6'l><'col-sm-6 text-right'B>>" +
            "<'row be-datatable-body'<'col-sm-12'tr>>" +
            "<'row be-datatable-footer'<'col-sm-5'i><'col-sm-7'p>>"
    });

</script>
@endsection
