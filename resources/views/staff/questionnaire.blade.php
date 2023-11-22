@extends('layout.sidebar')
@section('title')
    <title>Questionnaire</title>
@endsection
@section('page')
    <div class="container">
        <h3>Questionnaire</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Question</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($questionnaire as $key => $value)
                <tr>
                    <th scope="row">{{ ($key+1) }}</th>
                    <td>{{ $value['question'] }}</td>
                    <td>
                        <button type="button" class="btn-primary">Update</button>
                        <button type="button" class="btn-danger">Delete</button>
                    </td>
                </tr> 
                @endforeach
            </tbody>
        </table>
    </div>
@endsection