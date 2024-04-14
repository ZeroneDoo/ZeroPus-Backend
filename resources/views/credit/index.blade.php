@extends('template.layout')

@section('title')
    Credit - ZeroPus
@endsection

@section('header_title')
    Credit
@endsection

@section('content')
    <div class="card my-4" style="width: 100%; border-radius: 20px">
        <div class="card-body">
            {{-- <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" />
            </div> --}}
            <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modal">
                New Credit
            </button>
            <div class="table-responsive">
                <table id="table" class="table">
    
                </table>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Create or Update Credit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                    <form id="form" action="{{ route('credit.store') }}" method="POST">
                        <div class="modal-body">
                            @csrf
                            <div class="mb-3">
                                <label for="name">Name<sup style="color: red">*</sup></label>
                                <input type="text" class="form-control" name="name" id="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="price">Price<sup style="color: red">*</sup></label>
                                <input type="number" class="form-control" name="price" id="price" required>
                            </div>
                            <div class="mb-3">
                                <label for="amount">Amount<sup style="color: red">*</sup></label>
                                <input type="number" class="form-control" name="amount" id="amount" required>
                            </div>
                            <div class="mb-3 container">
                                <div class="form-check form-switch">
                                    <label for="is_active" style="display: flex; align-items: center; gap: 10px"><input class="form-check-input" type="checkbox" role="switch" id="is_active" state="true" name="is_active"><p style="margin: 0">Is Active</p></label>
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
                    url: "{{ route('credit.getData') }}",
                    type: 'GET',
                    dataSrc: "data"
                },
                columns: [
                    {
                        data: 'action',
                        title: 'Action',
                    },
                    { data: 'name', title: 'Name', orderable: true },
                    { 
                        data: 'price', 
                        title: 'Price', 
                        orderable: false,
                        render: (data, type, row, meta) => {
                            return `
                            <p>Rp.${row.price.toLocaleString('id', {minimumFractionDigits: 0})}</p>
                            `
                        } 
                    },
                    { 
                        data: 'amount', 
                        title: 'Amount',
                        orderable: false, 
                        render: (data, type, row, meta) => {
                            return `
                            <p>${row.amount.toLocaleString('id', {minimumFractionDigits: 0})}P</p>
                            `
                        }
                    },
                    { 
                        data: 'is_active', 
                        title: 'Is Active',
                        orderable: false,
                        render: (data, type, row, meta) => {
                            return `
                            <form class="container">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active_live" ${row.is_active ? "checked" : ""} state="true" role="switch" data-id="${row.id}" onchange="changeActive(this)">
                                </div>
                            </form>
                            `
                        }
                    },
                ]
            })

            setTimeout(() => {
                console.log(table.data().toArray())
            }, 3000);

            form.on("submit", (e) => {
                e.preventDefault()
                $("#is_active").val($("#is_active").is(":checked"))
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
                    },
                    error: () => {
                        $("#btn-submit").removeAttr("disabled")
                        $("#btn-close").removeAttr("disabled")
                        $("#btn-submit").html("Save Changes")
                    }
                });
            })
        });

        const changeActive = (button) => {
            const id = $(button).attr("data-id") 

            let action = "{{ route('credit.update', ':id') }}"
            action = action.replace(':id', id)

            $.ajax({
                type: "POST",
                url: action,
                data: JSON.stringify({
                    is_active: $(button).is(":checked")
                }),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    "Content-Type": "application/json"
                },
                beforeSend: () => {
                    $(button).attr("disabled", true)
                },
                success: (res) => {
                    table.ajax.reload()
                    $(button).removeAttr("disabled")
                    showAlert("Success to create or update credit", "success")
                },
                error: () => {
                    $(button).removeAttr("disabled")
                    showAlert("Faled to create or update credit", "error")
                }
            });
        }

        const getData = (button) => {
            const index = table.row($(button).closest('tr')).index()
            const dataTable = table.row(index).data()

            let action = "{{ route('credit.update', ':id') }}"
            action = action.replace(':id', dataTable['id'])

            console.log(dataTable)
            form.attr("action", action)
            form.find("input[name='name']").val(dataTable['name'])
            form.find("input[name='price']").val(dataTable['price'])
            form.find("input[name='amount']").val(dataTable['amount'])
            form.find("input[name='is_active']").prop("checked",dataTable['is_active'])
        }

        const deleteData = (e) => {
            let el = $(e);
            let id = el.data("id");
            $.ajax({
                type: "DELETE",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: `credit/delete/${id}`,
                beforeSend: () => {
                    el.html(`<label for="" style="display: flex; align-items: center; gap: 10px;margin: 0"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="loader"></span></label>`)
                    el.attr("disabled", true)
                },
                success: (res, textStatus, xhr) => {
                    table.ajax.reload()
                    showAlert("Success to delete credit", "success")
                },
                error: () => {
                    showAlert("Failed to delete credit", "error")
                }
            });
        };

        const clearModal = () => {
            $("#btn-submit").removeAttr("disabled")
            $("#btn-close").removeAttr("disabled")
            $("#btn-submit").html("Save Changes")
            $("#is_active").prop("checked", false)

            $("#modal").modal("hide")
            $('#form input').val('')

            form.attr("action", "{{ route('credit.store') }}")
        };
        $('[data-target="#modal"]').on('click', () => {
            clearModal()
        });
    </script>
@endpush