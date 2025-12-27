@extends('layouts.admin-layout')
@section('title', '- User Information')

@push('style')
  <style>
    .uploader {
      height: 17vh;
    }

    .img-fluid.img-prev {
      height: 100% !important;
    }

    .img-container {
      position: relative;
      display: inline-block;
    }

    .zoom-result {
      position: absolute;
      width: 200px;
      height: 200px;
      border: 1px solid #ccc;
      background-repeat: no-repeat;
      background-size: 700% 700%;
      display: none;
      pointer-events: none;
      z-index: 10;
      transition: top 0.1s ease, left 0.1s ease;
      /* smooth sliding */
    }


    @media (max-width: 375px) {
      .zoom-result {
        width: 150px;
        height: 150px;
      }
    }

    @media (max-width: 320px) {
      .zoom-result {
        width: 121px;
        height: 121px;
      }
    }

    @media (min-width: 275px) {
      .uploader {
        height: 21vh;
      }
    }

    @media (min-width: 350px) {
      .uploader {
        height: 25vh;
      }
    }

    @media (min-width: 425px) {
      .uploader {
        height: 31vh;
      }
    }

    @media (min-width: 575px) {
      .uploader {
        height: 25vh;
      }
    }

    @media (min-width: 768px) {
      .uploader {
        height: 37vh;
      }
    }

    @media (min-width: 992px) {
      .uploader {
        height: 35vh;
      }
    }

    @media (min-width: 1100px) {
      .uploader {
        height: 37vh;
      }
    }

    @media (min-width: 1200px) {
      .uploader {
        height: 31vh;
      }
    }

    @media (min-width: 1375px) {
      .uploader {
        height: 33vh;
      }
    }
  </style>
@endpush

@section('content')
  <div class="row">
    <x-admin.staff.profile-details :staff="$staff" />

    <x-admin.staff.personal-information :staff="$staff" />
  </div>
  <x-admin.staff.document-modal />
@endsection

@push('script')
  <script src="https://unpkg.com/zooming/build/zooming.min.js"></script>

  @if (Session::has('status'))
    <x-alert :msg="Session::get('msg')" :status="Session::get('status')" />
  @endif
  <script>
    // new Zooming().listen('.zoomable')
    document.addEventListener('DOMContentLoaded', function () {
      const userRoleDiv = document.getElementById('user-role')
      var modalEl = document.getElementById('documentModal');
      const frontSideBtn = document.getElementById('front-side-btn')
      const backSideBtn = document.getElementById('back-side-btn')
      const modalCloseBtn = document.querySelector('button.btn-close[data-bs-dismiss="modal"]');

      const userRole = "{{ $staff->role }}"
      userRoleDiv.textContent = toNormalText(userRole)

      var modal = new bootstrap.Modal(modalEl, { backdrop: true });

      modalEl.addEventListener('show.bs.modal', function () {
        document.body.appendChild(modalEl);
      });

      if (frontSideBtn) {
        frontSideBtn.addEventListener('click', function () {
          const img = this.querySelector('img'); // finds the image inside
          console.log(img);
          if (!img) return;
          const mainImage = document.getElementById("mainImage"); // your main display image
          mainImage.src = img.src;
        });
      }

      if (backSideBtn) {
        backSideBtn.addEventListener('click', function () {
          const img = this.querySelector('img');
          if (!img) return;
          const mainImage = document.getElementById("mainImage");
          mainImage.src = img.src;
        });
      }

      if (modalCloseBtn) {
        modalCloseBtn.addEventListener('click', function () {
          const img = document.getElementById("mainImage");
          img.src = "";
        });
      }



      const img = document.getElementById("mainImage");
      const result = document.getElementById("zoomResult");
      const offset = 20; // distance from cursor

      img.addEventListener("mousemove", function (e) {
        const rect = img.getBoundingClientRect();
        const containerRect = img.parentElement.getBoundingClientRect();

        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const xPercent = (x / img.width) * 100;
        const yPercent = (y / img.height) * 100;

        // Apply zoom
        result.style.backgroundImage = `url('${img.src}')`;
        result.style.backgroundPosition = `${xPercent}% ${yPercent}%`;
        result.style.display = "block";

        // Default position (below + right of cursor inside container)
        let left = e.clientX - containerRect.left + offset;
        let top = e.clientY - containerRect.top + offset;

        const containerWidth = containerRect.width;
        const containerHeight = containerRect.height;
        const zoomWidth = result.offsetWidth;
        const zoomHeight = result.offsetHeight;

        // Prevent overflow right
        if (left + zoomWidth > containerWidth) {
          left = e.clientX - containerRect.left - zoomWidth - offset;
        }

        // Prevent overflow bottom
        if (top + zoomHeight > containerHeight) {
          top = e.clientY - containerRect.top - zoomHeight - offset;
        }

        // Set final position
        result.style.left = left + "px";
        result.style.top = top + "px";
      });

      img.addEventListener("mouseleave", function () {
        result.style.display = "none";
      });

    })
  </script>
@endpush
