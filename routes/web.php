<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AssistantController;
use App\Http\Controllers\ReservationController;

Auth::routes();

Route::get(     "/", function (){return view(                                           "index");})->name(              "index");

Route::middleware(["Unverified"])->group(function(){
Route:: get(    "/verify",[                             UserController::class,          "verify"])->name(               "users.verify");
Route:: post(   "/verify",[                             UserController::class,          "sendVerification"])->name(     "users.sendVerification");
});
Route::middleware(["Admin", "Verified"])->group(function(){
Route::get(     "/admin",[                              AdminController::class,         "index"])->name(                "admins.index");
Route::get(     "/admin/users",[                        AdminController::class,         "users"])->name(                "admins.users");
Route::post(    "/admin/users",[                        AdminController::class,         "editUsers"])->name(            "admins.editUsers");
Route::get(     "/admin/users/verify",[                 AdminController::class,         "verify"])->name(               "admins.verify");
Route::post(    "/admin/users/verify",[                 AdminController::class,         "setVerify"])->name(            "admins.setVerify");
Route::get(     "/admin/faculties",[                    AdminController::class,         "faculties"])->name(            "admins.faculties");
Route::get(     "/admin/faculty/{faculty}",[            AdminController::class,         "faculty"])->name(              "admins.faculty");
Route::post(    "/admin/faculty/{faculty}",[            AdminController::class,         "editFaculty"])->name(          "admins.editFaculty");
Route::get(     "/admin/faculty/{faculty}/newmajor",[   AdminController::class,         "newMajor"])->name(             "admins.newMajor");
Route::post(    "/admin/faculty/{faculty}/newmajor",[   AdminController::class,         "insertMajor"])->name(          "admins.insertMajor");
Route::get(     "/admin/faculty-new",[                  AdminController::class,         "newFaculty"])->name(           "admins.newFaculty");
Route::post(    "/admin/faculty-new",[                  AdminController::class,         "insertFaculty"])->name(        "admins.insertFaculty");
Route::get(     "/admin/schedules/{monthid}",[          AdminController::class,         "schedules"])->name(            "admins.schedules");
Route::get(     "/admin/reservation/{id}", [            AdminController::class,         "reservation"])->name(          "admins.reservation");
Route::post(    "/admin", [                             AdminController::class,         "changeContactPerson"])->name(  "admins.changeContactPerson");
});
Route::middleware(["Verified"])->group(function(){
Route::get(     "/home", [                              HomeController::class,          "index"])->name(                "home");
Route::get(     "/reservations/my", [                   UserController::class,          "reservations"])->name(         "users.reservations");
Route::get(     "/reservations/my/{id}", [              UserController::class,          "reservation"])->name(          "users.reservation");
Route::delete(  "/reservations/my/{id}", [              ReservationController::class,   "cancelReservation"])->name(    "users.cancelReservation");
});
Route::middleware(["Assistant", "Verified"])->group(function(){
Route::get(     "/dashboard", [                         AssistantController::class,     "index"])->name(                "assistants.index");
Route::get(     "/dashboard/schedule", [                AssistantController::class,     "schedule"])->name(             "assistants.schedule");
Route::post(    "/dashboard/schedule", [                AssistantController::class,     "editSchedules"])->name(        "assistants.editSchedule");
Route::get(     "/dashboard/request/{id}", [            AssistantController::class,     "request"])->name(              "assistants.request");
Route::post(    "/dashboard/request/{id}", [            AssistantController::class,     "takeReservation"])->name(      "assistants.takeReservation");
Route::post(    "/reservations/my/{id}", [              AssistantController::class,     "completeReservation"])->name(  "assistants.completeReservation");
});
Route::middleware(["RegularUser", "Verified"])->group(function(){
Route::get(     "/reservations", [                      ReservationController::class,   "index"])->name(                "reservations.index");
Route::get(     "/reservation/{dateid}", [              ReservationController::class,   "make"])->name(                 "reservations.make");
Route::post(    "/reservation/{dateid}", [              ReservationController::class,   "insert"])->name(               "reservations.insert");
});

Route::get(     "auth/google/callback",[                UserController::class,          "handleGoogleCallback"]);