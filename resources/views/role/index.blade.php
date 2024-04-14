@extends('template.layout')

@section('title')
    Roles - ZeroPus
@endsection

@section('header_title')
    Roles
@endsection

@push('css')
    <style>
        .card-header .fa {
            transition: .3s transform ease-in-out;
        }
        .card-header .collapsed .fa {
            transform: rotate(90deg);
        }
        .grid-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            justify-content: center;
            align-items: center;
        }

    </style>
@endpush

@section('content')
    <div class="card my-4" style="width: 100%; border-radius: 20px">
        <div class="card-body">
            {{-- <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" />
            </div> --}}
            <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modal">
                New Roles
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
                    <h5 class="modal-title" id="exampleModalLongTitle">Create or Update Roles</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                    <form id="form" action="{{ route('role.store') }}" method="POST">
                        <div class="modal-body">
                            @csrf
                            <div class="mb-3">
                                <label for="name">Name<sup style="color: red">*</sup></label>
                                <input type="text" class="form-control" name="name" id="name" required>
                            </div>
                            {{-- permission --}}
                            <div class="mb-">
                                <div class="card">
                                    <h6 class="card-header">
                                        <a data-toggle="collapse" href="#card-collapse" aria-expanded="true" aria-controls="card-collapse" id="header-collapse" style="display: flex; justify-content: space-between">
                                            Permissions
                                            <i class="fa fa-chevron-down pull-right"></i>
                                        </a>
                                    </h6>
                                    <div id="card-collapse" class="collapse show container grid-container grid-container container p-4" aria-labelledby="header-collapse">
                                        @foreach ($permissions as $permission)
                                        <div class="container">
                                            <label for="permission-{{ $permission->id }}" style="display: flex; align-items: center; gap: 10px"><input class="form-check-input" type="checkbox" id="permission-{{ $permission->id }}" name="permissions[]" value="{{ $permission->id }}"><p style="margin: 0">{{ $permission->name }}</p></label>
                                        </div>
                                        @endforeach
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
                    url: "{{ route('role.getData') }}",
                    type: 'GET',
                    dataSrc: "data"
                },
                columns: [
                    {
                        data: 'action',
                        title: 'Action',
                    },
                    { 
                        data: 'name', 
                        title: 'Name', 
                        orderable: true,
                        render: (data, type, row, meta) => {
                            return `<h6><span class="badge badge-primary">${row.name}</span></h6>`
                        }
                    },
                    { data: 'created_at', title: 'Created At', orderable: true },
                    { data: 'updated_at', title: 'Updated At', orderable: true },
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
                        showAlert("Success to create or update role", "success")
                    },
                    error: () => {
                        $("#btn-submit").removeAttr("disabled")
                        $("#btn-close").removeAttr("disabled")
                        $("#btn-submit").html("Save Changes")
                        showAlert("Failed to create or update role", "error")
                    }
                });
            })
        });

        const getData = (button) => {
            const index = table.row($(button).closest('tr')).index()
            const dataTable = table.row(index).data()

            let action = "{{ route('role.update', ':id') }}"
            action = action.replace(':id', dataTable['id'])

            console.log(dataTable)
            form.attr("action", action)
            form.find("input[name='name']").val(dataTable['name'])
            form.find('input[name="permissions[]"]').each((i, e) => {
                const permission = parseInt($(e).val());

                // Check if the value exists in the data array
                if (dataTable['permissions'].includes(permission)) {
                    // If it exists, check the checkbox
                    $(e).prop('checked', true);
                }else{
                    $(e).prop('checked', false);
                }
            });
        }

        const deleteData = (e) => {
            let el = $(e);
            let id = el.data("id");
            $.ajax({
                type: "DELETE",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: `role/delete/${id}`,
                beforeSend: () => {
                    el.html(`<label for="" style="display: flex; align-items: center; gap: 10px;margin: 0"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="loader"></span></label>`)
                    el.attr("disabled", true)
                },
                success: (res, textStatus, xhr) => {
                    table.ajax.reload()
                    showAlert("Success to delete role", "success")
                },
                error: () => {
                    showAlert("Failed to delete role", "error")
                }
            });
        };

        const clearModal = () => {
            $("#btn-submit").removeAttr("disabled")
            $("#btn-close").removeAttr("disabled")
            $("#btn-submit").html("Save Changes")

            $("#modal").modal("hide")
            $("#form input[name='name']").val('')
            $("input[name='permissions[]']").prop("checked", false)

            form.attr("action", "{{ route('role.store') }}")
        };
        $('[data-target="#modal"]').on('click', () => {
            clearModal()
        });
    </script>
@endpush