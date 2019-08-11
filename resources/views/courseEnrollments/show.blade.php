<?php
/**
 * @var \App\CourseEnrollment $enrollment
 */
?>
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <h2 class="card-header">Lessons</h2>
                    <div class="card-body">
                        <ol>
                            @foreach($enrollment->course->lessons as $lesson)
                                <li>
                                    <a href="{{ route('lessons.show', ['slug' => $enrollment->course->slug, 'number' => $lesson->number]) }}">
                                        {{ $lesson->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </div>

                <div class="card mt-4">
                    <h2 class="card-header">Statistics</h2>
                    <div class="card-body">

                        <p>
                            Your rankings improve every time you answer a question correctly.
                            Keep learning and earning course points to become one of our top learners!
                        </p>
                        <div class="row">
                            <div class="col-md-6">
                                <h4>You are ranked <b>{{ $countryRank }}</b> in {{ auth()->user()->country->name }}</h4>
                                <ul style="padding: 0px;">
                                    @foreach ($slicedScores['country'] as $place => $scoreData)
                                        <li class="courseRanking__rankItem"
                                            style="display: flex; flex-direction: row; padding: 10px;">
                                            <div class="position"
                                                style="font-size: 28px; color: rgb(132, 132, 132); text-align: right; width: 80px; padding-right: 10px;">
                                                {{ $place + 1 }}
                                            </div>
                                            <div class="info">
                                                <div style="font-size: 16px;">
                                                    {!! $scoreData->user_id === auth()->user()->id ? '<b>' : '' !!}
                                                        {{ $scoreData->user_name }}
                                                    {!! $scoreData->user_id === auth()->user()->id ? '</b>' : '' !!}
                                                </div>
                                                <div class="score" style="font-size: 10px; color: rgb(132, 132, 132);">
                                                    {{ $scoreData->score }} PTS (+93 -- TODO --)
                                                </div>
                                            </div>
                                        </li>
                                        {!! !$loop->last && !isset($slicedScores['country'][$place+1]) ? '<hr />' : '' !!}
                                    @endforeach
                                </ul>
                            </div>

                            <div class="col-md-6">
                                <h4>You are ranked <b>{{ $globalRank }}</b> Worldwide</h4>
                                <ul style="padding: 0px;">
                                    @foreach ($slicedScores['global'] as $place => $scoreData)
                                        <li class="courseRanking__rankItem"
                                            style="display: flex; flex-direction: row; padding: 10px;">
                                            <div class="position"
                                                style="font-size: 28px; color: rgb(132, 132, 132); text-align: right; width: 80px; padding-right: 10px;">
                                                {{ $place + 1 }}
                                            </div>
                                            <div class="info">
                                                <div style="font-size: 16px;">
                                                    {!! $scoreData->user_id === auth()->user()->id ? '<b>' : '' !!}
                                                        {{ $scoreData->user_name }}
                                                    {!! $scoreData->user_id === auth()->user()->id ? '</b>' : '' !!}
                                                </div>
                                                <div class="score" style="font-size: 10px; color: rgb(132, 132, 132);">
                                                    {{ $scoreData->score }} PTS (+93 -- TODO --)
                                                </div>
                                            </div>
                                        </li>
                                        {!! !$loop->last && !isset($slicedScores['global'][$place+1]) ? '<hr />' : '' !!}
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
