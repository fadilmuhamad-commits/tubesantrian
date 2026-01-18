@props(['client' => false, 'data' => []])

<!-- {{-- Card antrian: struktur utama --}}
<div data-component="ambil-antrian-card">
    {{-- existing code --}}
</div> -->

<style>
  .btn-outline-primary:hover {
    color: #fff;
    background-color: #cf1427;
    border-color: #cf1427;
  }

  #antrian-grid {
    grid-template-columns: repeat(3, auto);
  }

  /* #cetak-title {
    font-size: calc(1.2rem + 1.5vw);
  } */

  #cetak-subtitle {
    font-size: calc(1rem + 0.3vw);
  }

  @media(max-width: 1200px) {
    #antrian-grid {
      grid-template-columns: repeat(2, auto);
    }
  }

  @media(max-width: 992px) {
    #antrian-grid {
      grid-template-columns: repeat(1, 1fr);
    }
  }

  @media(max-width: 768px) {
    #antrian-grid {
      grid-template-columns: repeat(1, 1fr);
    }

    /* #cetak-title {
      margin-top: 20px;
      font-size: calc(1rem + 1.5vw);
    } */

    #cetak-subtitle {
      font-size: calc(0.7rem + 0.3vw);
      margin-bottom: 5px
    }
  }

  @media(max-width: 512px) {
    #antrian-grid {
      grid-template-columns: repeat(1, 1fr);
    }
  }
</style>

<div class="container d-flex flex-column text-center justify-content-center w-100">
  @if ($client)
    {{-- <span id="cetak-title" class="fw-bold">Ambil Nomor Antrian</span> --}}
    <div id="cetak-subtitle">Pilih salah satu kategori loket di bawah ini</div>
    <hr>
  @endif

  <div id="antrian-grid" class="d-grid justify-content-center gap-3">
    {{-- @foreach ($data as $loket)
      <form method="POST" onsubmit="ambilSubmit({{ $loket->id }})"
        action="{{ $client ? route('tanya-opsi', $loket->id) : route('ambil-antrian.store', $loket->id) }}">
        @csrf
        <button type="{{ $client ? 'submit' : 'button' }}" class="col text-decoration-none btn p-0" style="border: none"
          data-bs-toggle="modal" data-bs-target="#modal-{{ $loket->id }}" {{ $loket->Lokets ? 'disabled' : '' }}>
          <div class="card position-relative overflow-hidden border-0 {{ $loket->Lokets ? 'opacity-50' : '' }}"
            style="min-width: 240px; min-height: 120px; background-color: {{ $loket->Lokets ? $loket->color : '#1e1e1e' }};">
            <div class="card-body d-flex align-items-center justify-content-center">
              <h5 class="card-title mb-0 fw-medium text-white" style="text-shadow: 1px 1px 1px black; font-size: 24px;">
                {{ $loket->name }}</h5>
            </div>

            <div
              class="position-absolute text-white top-0 start-0 w-100 h-100 d-flex align-items-end justify-content-center pb-2"
              style="font-size: 12px; text-shadow: 1px 1px 1px black;">
              Status : <span
                class="fw-bold ms-1 px-1 {{ $loket->Lokets ? 'text-bg-danger' : 'text-bg-success' }}">{{ $loket->Lokets ? 'Nonaktif' : 'Aktif' }}</span>
            </div>
          </div>
        </button>

        @if ($loket->Lokets && !$client)
          <div class="modal fade" tabindex="-1" id="modal-{{ $loket->id }}">
            <div class="modal-dialog modal-dialog-centered" style="width: fit-content;">
              <div class="modal-content py-3 px-5">
                <div class="modal-body text-center">
                  <p>Apakah Anda Yakin Ingin Memilih<br><b class="text-tertiary"
                      style="font-size: 33px">{{ $loket->name }}?</b></p>
                </div>
                <div class="modal-footer border-0 d-flex justify-content-center pt-0">
                  <button type="button" class="btn btn-outline-secondary fw-bold"
                    data-bs-dismiss="modal">Tidak</button>
                  <button type="submit" class="btn btn-primary px-4 fw-bold" id="btn-{{ $loket->id }}">Ya</button>
                </div>
              </div>
            </div>
          </div>
        @endif

      </form>
    @endforeach --}}
  </div>
</div>

<script>
  const isClient = @json($client);
  const baseUrl = @json(url('/'));

  function updateData(lokets) {
    let html = '';

    lokets.forEach(loket => {
      const hasActiveLoket = loket.counters.find(e => e.status === 1);
      const actionRoute = isClient ? `{{ route('tanya-opsi', 9999) }}`.replace(9999, loket.id) :
        `{{ route('ambil-antrian.store', 9999) }}`.replace(9999, loket.id);

      html += `
      <form id="ambil-form-${loket.id}" method="POST" onsubmit="ambilSubmit(${loket.id})"
        action="${actionRoute}">
        @csrf
        <button type="{{ $client ? 'submit' : 'button' }}"
          onclick="${!isClient ? 'ambilSubmit(' + loket.id + ')' : ''}"
          class="col text-decoration-none btn p-0 btn-cetak w-100"
          style="border: none" ${ !hasActiveLoket ? 'disabled' : '' }>
          <div class="card position-relative overflow-hidden border-0 ${ !hasActiveLoket ? 'opacity-50' : '' }"
            style="min-width: 344px; min-height: 120px; background-color: ${ hasActiveLoket ? loket.color : '#1e1e1e' };">
            <div class="card-body d-flex align-items-center justify-content-center">
              <h5 class="card-title mb-0 fw-medium text-white" style="text-shadow: 1px 1px 1px black; font-size: 24px;">
                ${ loket.name }</h5>
            </div>

            <div
              class="position-absolute text-white top-0 start-0 w-100 h-100 d-flex align-items-end justify-content-center pb-2"
              style="font-size: 12px">
              Status : <span
                class="fw-bold ms-1 px-1 ${ !hasActiveLoket ? 'text-bg-danger' : 'text-bg-success' }">${ !hasActiveLoket ? 'Nonaktif' : 'Aktif' }</span>
            </div>
          </div>
        </button>
      </form>
      `;
    });

    return html;
  }

  function generatePDF(tokenParam) {
    $.ajax({
      type: 'GET',
      url: '{{ route('ambil-antrian.pdf') }}',
      data: {
        token: tokenParam
      },
      success: function(response) {
        console.log('PDF generated successfully');
        printPDF(response.url);
      },
      error: function(xhr, status, error) {
        console.error(error);
      }
    });
  }

  function printPDF(pdfData) {
    let iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    document.body.appendChild(iframe);

    iframe.src = pdfData;

    iframe.onload = function() {
      iframe.contentWindow.print();

      setTimeout(hideMainSpinner, 5000);
    };
  }

  function ambilSubmit(id) {
    $('.btn-cetak').prop('disabled', true);

    if (!isClient) {
      showMainSpinner();

      $.ajax({
        type: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: `{{ route('ambil-antrian.store', 9999) }}`.replace('9999', id),
        success: function(response) {
          generatePDF(response.token);
        },
        error: function(error) {
          console.error(error);
        }
      });
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('antrian-grid');
    let loketsData = @json($data);

    // GET DATA ON FOCUS ONLY
    let visibilityChange = false;
    document.addEventListener("visibilitychange", function() {
      if (document.hidden) {
        visibilityChange = true;
      } else {
        visibilityChange = false;
        getCetakData();
      }
    });

    function getCetakData() {
      $.ajax({
        url: '{{ route('ajax.cetak') }}',
        type: 'GET',
        success: function(res) {
          loketsData = res.categories;
          container.innerHTML = updateData(loketsData);

          if (!visibilityChange) {
            setTimeout(getCetakData, 3000);
          }
        },
        error: function(xhr, status, error) {
          console.error(error);
          if (!visibilityChange) {
            setTimeout(getCetakData, 3000);
          }
        }
      })
    }
    getCetakData();

    // WEBSOCKET
    // window.Echo.channel('cetak')
    //   .listen('.WS_Cetak', (e) => {
    //     loketsData = e.data.loket;

    //     container.innerHTML = updateData(loketsData);
    //   });
  });
</script>
