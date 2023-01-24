<?php

namespace io3x1\LaravelAuthBuilder\Interfaces;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

interface Auth
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function register(\Illuminate\Http\Request $request): JsonResponse|RedirectResponse;

    /**
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function login(\Illuminate\Http\Request $request): JsonResponse|RedirectResponse;

    /**
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function logout(\Illuminate\Http\Request $request): JsonResponse|RedirectResponse;

    /**
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function resend(\Illuminate\Http\Request $request): JsonResponse|RedirectResponse;

    /**
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function profile(\Illuminate\Http\Request $request): JsonResponse|RedirectResponse;

    /**
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function update(\Illuminate\Http\Request $request): JsonResponse|RedirectResponse;

    /**
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function password(\Illuminate\Http\Request $request): JsonResponse|RedirectResponse;

    /**
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function reset(\Illuminate\Http\Request $request): JsonResponse|RedirectResponse;

    /**
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function otp(\Illuminate\Http\Request $request): JsonResponse|RedirectResponse;

    /**
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function destroy(\Illuminate\Http\Request $request): JsonResponse|RedirectResponse;
}
