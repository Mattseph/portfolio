<?php

use App\Livewire\Pages\About;
use App\Livewire\Pages\Contact;
use App\Livewire\Pages\Home;
use App\Livewire\Pages\ProjectIndex;
use App\Livewire\Pages\ProjectShow;
use App\Livewire\Pages\Services;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
Route::get('/about', About::class)->name('about');
Route::get('/projects', ProjectIndex::class)->name('projects.index');
Route::get('/projects/{slug}', ProjectShow::class)->name('projects.show');
Route::get('/services', Services::class)->name('services');
Route::get('/contact', Contact::class)->name('contact');
