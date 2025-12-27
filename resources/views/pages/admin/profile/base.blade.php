@extends('layouts.admin-layout')
@section('title', 'Profile')

@push('style')
  <x-admin.profile.styles />
@endpush

@section('content')
  <form action="{{ route('admin.profile.store_or_update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
      <x-admin.profile.profile-details />

      <x-admin.profile.personal-information />
    </div>
  </form>
@endsection

@push('script')
  @if (Session::has('status'))
    <x-alert :msg="Session::get('msg')" :status="Session::get('status')" />
  @endif
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const frontSide = document.getElementById('front-side')
      const frontSideUploader = document.getElementById('front-side-uploader')
      const backSide = document.getElementById('back-side')
      const backSideUploader = document.getElementById('back-side-uploader')
      const imgPreviewFront = document.getElementById('img-preview-front')
      const frontContent = document.getElementById('front-side-content')
      const frontSideRemoverBtn = document.getElementById('front-side-remover')
      const imgPreviewBack = document.getElementById('img-preview-back')
      const backContent = document.getElementById('back-side-content')
      const backSideRemoverBtn = document.getElementById('back-side-remover')
      //const addBtn = document.getElementById('add-btn')
      //const addBtnDiv = document.getElementById('add-btn-div')
      const avatarUploaderBtn = document.getElementById('avatar-uploader-btn')
      const avatarInput = document.getElementById('avatar')
      const avatarPreview = document.getElementById('avatar-preview')
      const editBtn = document.getElementById('edit-btn')
      const inputs = document.querySelectorAll('.input')
      const submitBtn = document.querySelector('.submit-btn')
      const socialMediaInput = document.getElementById('social-media-input')
      const deleteFrontSideImgBtn = document.getElementById('delete-front-side-img')
      const deleteBackSideImgBtn = document.getElementById('delete-back-side-img')
      const spinner = document.getElementById('spinner-border')
      const userRoleDiv = document.getElementById('user-role')
      const userRole = "{{ auth()->user()->role }}"
      userRoleDiv.textContent = userRole === "owner" || userRole === "admin" || userRole === "super_admin" ? "Shop "+toNormalText(userRole) : toNormalText(userRole)
      avatarInput.value = ''
      frontSide.value = ''
      backSide.value = ''

      inputs.forEach(function (input) {
        input.disabled = true
      })
      //addBtn.disabled = true
      submitBtn.disabled = true

      frontSideUploader.addEventListener('click', function () {
        frontSide.click()
      })
      backSideUploader.addEventListener('click', function () {
        backSide.click()
      })

      frontSide.addEventListener('change', function (e) {
        const file = e.target.files[0]
        imgPreviewFront.firstElementChild.src = ''
        const reader = new FileReader()
        reader.onload = function (e) {
          const image = new Image()
          image.src = e.target.result
          image.onload = function () {
            imgPreviewFront.classList.remove('d-none')
            frontSideRemoverBtn.classList.remove('d-none')
            if (deleteFrontSideImgBtn !== null) {
              deleteFrontSideImgBtn.classList.add('d-none')
            }
            frontContent.classList.add('d-none')
            imgPreviewFront.firstElementChild.src = e.target.result
          }
        }
        reader.readAsDataURL(file)
      })
      backSide.addEventListener('change', function (e) {
        const file = e.target.files[0]
        const reader = new FileReader()
        reader.onload = function (e) {
          const image = new Image()
          image.src = e.target.result
          image.onload = function () {
            imgPreviewBack.classList.remove('d-none')
            backSideRemoverBtn.classList.remove('d-none')
            if (deleteBackSideImgBtn !== null) {
              deleteBackSideImgBtn.classList.add('d-none')
            }
            backContent.classList.add('d-none')
            imgPreviewBack.firstElementChild.src = e.target.result
          }
        }
        reader.readAsDataURL(file)
      })
      avatarInput.addEventListener('change', function (e) {
        const file = e.target.files[0]
        const reader = new FileReader()
        reader.onload = function (e) {
          avatarPreview.src = e.target.result
        }
        reader.readAsDataURL(file)
      })

      frontSideRemoverBtn.addEventListener('click', function () {
        frontSide.value = ''
        imgPreviewFront.firstElementChild.src = ''
        imgPreviewFront.classList.add('d-none')
        frontContent.classList.remove('d-none')
        frontSideRemoverBtn.classList.add('d-none')
      })
      backSideRemoverBtn.addEventListener('click', function () {
        backSide.value = ''
        imgPreviewBack.firstElementChild.src = ''
        imgPreviewBack.classList.add('d-none')
        backContent.classList.remove('d-none')
        backSideRemoverBtn.classList.add('d-none')
      })

      //addBtn.addEventListener('click', function () {
      //let newDivWithInput = `<div class="col-xl-6">
      //<div class="input-group mb-2">
      //<input type="text" name="socials[]" class="form-control" placeholder="Social Media" />
      //<button type="button" class="btn btn-sm btn-danger social-remove-btn">
      //<i class="fa-solid fa-xmark"></i>
      //</button>
      //</div>
      //</div>`
      //addBtnDiv.insertAdjacentHTML('beforebegin', newDivWithInput)
      //})

      avatarUploaderBtn.addEventListener('click', function () {
        avatarInput.click()
      })

      editBtn.addEventListener('click', function () {
        inputs.forEach(function (input) {
          if (input.disabled === true) {
            input.disabled = false
            //addBtn.disabled = false
            submitBtn.disabled = false
            avatarUploaderBtn.classList.remove('d-none')
          } else {
            input.disabled = true
            //addBtn.disabled = true
            submitBtn.disabled = true
            avatarUploaderBtn.classList.add('d-none')
          }
        })
        if (frontSideRemoverBtn.classList.contains('disabled')) frontSideRemoverBtn.classList.remove('disabled')
        else frontSideRemoverBtn.classList.add('disabled')
        if (backSideRemoverBtn.classList.contains('disabled')) backSideRemoverBtn.classList.remove('disabled')
        else backSideRemoverBtn.classList.add('disabled')
      })

      submitBtn.addEventListener('click', function () {
        spinner.classList.remove('d-none')
        submitBtn.classList.add('opacity-50')
      })
    })
  </script>
@endpush
