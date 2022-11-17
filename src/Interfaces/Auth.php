<?php

namespace io3x1\LaravelAuthBuilder\Interfaces;

interface Auth
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse;

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse;

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse;

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resend(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse;

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse;

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse;

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function password(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse;

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse;

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function otp(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse;

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse;
}
