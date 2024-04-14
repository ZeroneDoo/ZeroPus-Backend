@extends('template.auth')

@section('title')
    Login - Zeropus
@endsection

@section('content')
<div class="card" style="padding: 23px; width: 35%; border-radius:20px">
    <div class="card-body">
        <div style="text-align: center" class="mb-4">
            <p style="font-size: 22px; font-weight: 600;line-height: 20px">Zeropus</p>
            <p style="font-size: 24px; font-weight: 600; line-height: 20px">Login</p>
        </div>
        <form action="{{ route('login.store') }}" id="form" method="POST" data-url="{{ route('dashboard') }}">
            @csrf
            <div class="mb-3">
                <label for="email">Email<sup style="color: red">*</sup></label>
                <input type="email" class="form-control" name="email" id="email" required value="{{  isset($_COOKIE['remember']) ? $_COOKIE['remember'] :'' }}">
            </div>
            <div class="mb-3">
                <label for="password">Password<sup style="color: red">*</sup></label>
                <input type="password" class="form-control" name="password" id="password" required>
            </div>
            <div class="mb-3">
                <label for="remember" style="display: flex; align-items: center; gap: 10px"><input style="width: 17px; height: 17px; " type="checkbox" id="remember" name="remember" {{ isset($_COOKIE['remember']) ? "checked" : '' }}><p style="margin: 0">Remember me</p></label>
            </div>
            <button class="btn btn-primary" style="width: 100%;" id="btn-login">
                Login
            </button>
        </form>
    </div>
</div>
@endsection

@push('js')
    <script>
        $(document).ready(() => {
            const form = $("#form")
            form.on("submit", (e)=>{
                e.preventDefault()
                console.log(form.attr("method"))
                console.log(form.attr("action"))
                $.ajax({
                    type: form.attr("method"),
                    url: form.attr("action"),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: new FormData(form[0]),
                    processData: false,
                    contentType: false,
                    beforeSend: () => {
                        $("#btn-login").attr("disabled", true)
                        $("#btn-login").html(`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="loader"></span>`)
                    },
                    success: (res, textStatus, xhr) => {
                        if(xhr.status === 200) {
                            console.log(form.attr("data-url"))
                            location.href = form.attr("data-url")
                        }
                    },
                    error: () => {
                        showAlert("Email or username is not valid", "error")
                        $("#btn-login").removeAttr("disabled")
                        $("#btn-login").html(`Login`)
                    }
                });
            })
        });
    </script>
@endpush