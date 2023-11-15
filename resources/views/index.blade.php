@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <button onclick="startFCM()" class="btn btn-danger btn-flat">Allow notification
            </button>
            <div class="card mt-3">
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <form action="{{ route('send.web-notification') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            {{-- <label>Message Title</label> --}}
                            <label for="">Name</label>
                            <input type="text" class="form-control" name="name">
                        </div>
                        <div class="form-group">
                            {{-- <label>Message Body</label> --}}
                            <label for="">description</label>
                            <textarea class="form-control" name="description"></textarea>
                        </div>
                        <div>
                            <label for="">Price</label>
                            <input type="text" class="form-control" name="price">
                        </div>
                        <button type="submit" class="btn btn-success btn-block">Create</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <table class="table">
                <thead>
                    <tr>
                        <!-- <th>ID</th> -->
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                    <tr>
                        <!-- <td>{{ $product->id }}</td> -->
                        <td>{{ $product->name }}</td>
                        <td>
                            @if (strlen($product->description) > 50)
                                {{ substr($product->description, 0, 50) . '...' }}
                            @else
                                {{ $product->description }}
                            @endif
                        </td>
                        <td>{{ $product->price }}</td>
                        <td>
                            <a href="{{ route('send-notification', ['product_id' => $product->id]) }}">Gửi thông báo</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination">
                @if ($products->currentPage() > 1)
                <a href="{{ $products->previousPageUrl() }}" class="btn btn-primary">Trang trước</a>
                @endif

                <span>Trang {{ $products->currentPage() }}/{{ $products->lastPage() }}</span>

                @if ($products->hasMorePages())
                <a href="{{ $products->nextPageUrl() }}" class="btn btn-primary">Trang sau</a>
                @endif
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
    <div class="col-md-8">
        <table class="table">
            <thead>
                <tr>
                    <th>ID Product</th>
                    <th>Token Người Gửi</th>
                    <th>Thành công</th>
                    <th>Thất bại</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($push_notifications as $push)
                    <tr>
                        <td>{{ $push->product_id }}</td>
                        <td>{{ $push->device_token }}</td>
                        <td>{{ $push->success }}</td>
                        <td>{{ $push->failure }}</td>
                        @if ($push->failure === 1)
                            <td>
                                <a href="{{ route('send-retry', ['product_id' => $push->product_id]) }}">Retry</a>
                            </td>
                        @else
                            <td></td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


</div>

<!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.3.2/firebase.js"></script>

<script>
const firebaseConfig = {
    apiKey: "AIzaSyDlXc7Zceehcn-2kM7AXloUCRpqDyRIzYY",
    authDomain: "push-notification-d9539.firebaseapp.com",
    projectId: "push-notification-d9539",
    storageBucket: "push-notification-d9539.appspot.com",
    messagingSenderId: "585225948031",
    appId: "1:585225948031:web:4730e7cc736d76c484284a",
    measurementId: "G-74RB5C2JCH"
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

function startFCM() {
    messaging
        .requestPermission()
        .then(function() {
            return messaging.getToken()
        })
        .then(function(response) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{ route("store.token") }}',
                type: 'POST',
                data: {
                    token: response
                },
                dataType: 'JSON',
                success: function(response) {
                    alert('Token stored.');
                },
                error: function(error) {
                    alert(error);
                },
            });
        }).catch(function(error) {
            alert(error);
        });
}
messaging.onMessage(function(payload) {
    console.log(payload);

    const title = payload.notification.title;
    const options = {
        body: payload.notification.body,
        icon: "http://www.google.com/images/branding/googlelogo/2x/googlelogo_color_92x30dp.png",
        image: "http://www.google.com/images/branding/googlelogo/2x/googlelogo_color_92x30dp.png"

    };
    new Notification(title, options);
});
</script>
@endsection
