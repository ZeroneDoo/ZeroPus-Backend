@extends('template.layout')

@section('title')
    User - ZeroPus
@endsection

@section('header_title')
    User
@endsection

@section('content')
    <div class="card my-4" style="width: 100%; border-radius: 20px">
        <div class="card-body">
            <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modal">
                New User
            </button>
            <div class="table-responsive">
                <table id="table" class="table">
    
                </table>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Create or Update User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                    <form id="form" action="{{ route('user.store') }}" method="POST" enctype="multipart/form-data">
                        <div class="modal-body row">
                            @csrf
                            <div class="col">
                                <div class="mb-3">
                                    <label for="name">Name<sup style="color: red">*</sup></label>
                                    <input type="text" class="form-control" name="name" id="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="username">Username<sup style="color: red">*</sup></label>
                                    <input type="text" class="form-control" name="username" id="username" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email">Email<sup style="color: red">*</sup></label>
                                    <input type="text" class="form-control" name="email" id="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="no_telp">No. Telp<sup style="color: red">*</sup></label>
                                    <input type="text" class="form-control" name="no_telp" id="no_telp" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" name="password" id="password">
                                </div>
                                <div class="mb-3">
                                    <label for="credit">Credit<sup style="color: red">*</sup></label>
                                    <input type="number" class="form-control" name="credit" id="credit" required>
                                </div>
                                <div class="mb-3">
                                    <label for="alamat">Alamat</label>
                                    <textarea class="form-control" name="alamat" id="alamat"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="role">Role<sup style="color: red">*</sup></label>
                                    <select class="form-select" id="role" name="role" data-placeholder="Choose anything" required>
                                        <option value=""></option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{-- file --}}
                            <div class="col">
                                <div class="mb-3">
                                    <label for="photo">Photo</label>
                                    <div>
                                        <div class="mb-4 d-flex justify-content-center">
                                            <img id="selectedImage" src="https://mdbootstrap.com/img/Photos/Others/placeholder.jpg" 
                                            alt="example placeholder" class="rounded-circle" style="width: 200px; height: 200px; object-fit: cover;" />
                                        </div>
                                        <div class="d-flex justify-content-center">
                                            <div class="btn btn-primary btn-rounded">
                                                <label class="form-label text-white m-1" style="cursor: pointer" for="profile">Choose file</label>
                                                <input type="file" accept="image/*" class="form-control d-none" id="profile" name="profile" onchange="displaySelectedImage(event, 'selectedImage')" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" id="btn-close" data-dismiss="modal">Close</button>
                            <button class="btn btn-primary" id="btn-submit">Save Changes</button>
                        </div>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        let table
        const form = $("#form")

        $(document).ready( () => {
            table = $("#table").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('user.getData') }}",
                    type: 'GET',
                    dataSrc: "data"
                },
                columns: [
                    {
                        data: 'action',
                        title: 'Action',
                    },
                    { data: 'name', title: 'Name', orderable: true },
                    { data: 'username', title: 'Username', orderable: true },
                    { data: 'email', title: 'Email', orderable: true },
                    { 
                        data: 'role', 
                        title: 'Role', 
                        orderable: true,
                        render: (data, type, row, meta) => {
                            return `<span class="badge rounded-pill bg-primary">${row?.role?.name ?? ''}</span>`
                        }
                    },
                ]
            })

            setTimeout(() => {
                console.log(table.data().toArray())
            }, 3000);

            form.on("submit", (e) => {
                e.preventDefault()
                let data = new FormData($("#form")[0]);
                $.ajax({
                    type: form.attr("method"),
                    url: form.attr("action"),
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    processData: false,
                    contentType: false,
                    beforeSend: () => {
                        $("#btn-submit").attr("disabled", true)
                        $("#btn-close").attr("disabled", true)
                        $("#btn-submit").html(`<label for="" style="display: flex; align-items: center; gap: 10px;margin: 0"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="loader"></span><p style="margin: 0">Save changes</p></label>`)
                    },
                    success: (res, textStatus, xhr) => {
                        table.ajax.reload()
                        clearModal()
                        showAlert("Success to create or update user", "success")
                    },
                    error: () => {
                        $("#btn-submit").removeAttr("disabled")
                        $("#btn-close").removeAttr("disabled")
                        $("#btn-submit").html("Save Changes")
                        showAlert("Failed to create or update user", "error")
                    }
                });
            })
        });

        // role
        $( '#role' ).select2( {
            theme: "bootstrap-5",
            width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
            placeholder: $( this ).data( 'placeholder' ),
        } );

        const displaySelectedImage = (event, elementId) => {
            const selectedImage = document.getElementById(elementId);
            const fileInput = event.target;

            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    selectedImage.src = e.target.result;
                };

                reader.readAsDataURL(fileInput.files[0]);
            }
        }

        const getData = (button) => {
            const index = table.row($(button).closest('tr')).index()
            const dataTable = table.row(index).data()

            let action = "{{ route('user.update', ':id') }}"
            action = action.replace(':id', dataTable['id'])

            console.log(dataTable)
            form.attr("action", action)
            form.find("input[name='name']").val(dataTable['name'])
            form.find("input[name='username']").val(dataTable['username'])
            form.find("input[name='email']").val(dataTable['email'])
            form.find("input[name='no_telp']").val(dataTable['no_telp'])
            form.find("input[name='credit']").val(dataTable['credit'])
            form.find("textarea[name='alamat']").text(dataTable['alamat'])
            form.find("select[name='role']").val(dataTable['role'].id)
            form.find("#selectedImage").attr("src",dataTable['profile'])
        }

        const deleteData = (e) => {
            let el = $(e);
            let id = el.data("id");
            $.ajax({
                type: "DELETE",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: `user/delete/${id}`,
                beforeSend: () => {
                    el.html(`<label for="" style="display: flex; align-items: center; gap: 10px;margin: 0"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="loader"></span></label>`)
                    el.attr("disabled", true)
                },
                success: (res, textStatus, xhr) => {
                    table.ajax.reload()
                    showAlert("Success to delete user", "success")
                },
                error: () => {
                    showAlert("Failed to delete user", "error")
                }
            });
        };

        const clearModal = () => {
            $("#btn-submit").removeAttr("disabled")
            $("#btn-close").removeAttr("disabled")
            $("#btn-submit").html("Save Changes")
            $("#selectedImage").attr("src","https://mdbootstrap.com/img/Photos/Others/placeholder.jpg")

            $("#modal").modal("hide")
            $('#form input').val('')

            form.find("input[name='is_active']").val("checked")
            form.attr("action", "{{ route('user.store') }}")
        };
        $('[data-target="#modal"]').on('click', () => {
            clearModal()
        });
    </script>
@endpush