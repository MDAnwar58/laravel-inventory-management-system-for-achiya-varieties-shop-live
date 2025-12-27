@props([
'latest_orders' => []
])
<div class="col-12 col-lg-12 d-flex">
    <div class="card flex-fill">
        <div class="card-header px-3 pb-0 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Latest Orders</h5>
            <a href="{{route('admin.sales.orders')}}" class="btn btn-sm btn-light border shadow rounded-3 fw-medium fs-5">Show All</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover my-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Order Date</th>
                        <th>Due Date</th>
                        <th>Payment Status</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($latest_orders as $order)
                    <tr>
                        <td>
                            <div style="width: 150px;">
                                {{$order->customer->name}}
                            </div>
                        </td>
                        <td>
                            <div style="width: 90px;">{{date('d/m/Y', strtotime($order->order_date))}}</div>
                        </td>
                        <td>
                            <div style="width: 90px;">{{$order->due_date ? date('d/m/Y', strtotime($order->due_date)) : 'N/A'}}</div>
                        </td>
                        <td>
                            <div style="width: 110px;">
                                @if($order->payment_status === "paid")
                                <span class="badge bg-success-subtle text-success">Paid</span>
                                @elseif($order->payment_status === "due")
                                <span class="badge bg-warning-subtle text-warning">Due</span>
                                @elseif($order->payment_status === "partial due")
                                <span class="badge bg-info-subtle text-info">Partial Due</span>
                                @else
                                <span class="badge bg-danger-subtle text-danger">Cancel</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($order->status === "confirmed")
                            <span class="badge bg-success">Confirmed</span>
                            @else
                            <span class="badge bg-danger">Cancelled</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{route('admin.sales.order.invoice', $order->id)}}" class="btn btn-sm btn-outline-info">

                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
