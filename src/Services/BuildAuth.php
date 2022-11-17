<?php

namespace io3x1\LaravelAuthBuilder\Services;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use io3x1\LaravelAuthBuilder\Events\SendOTP;
use io3x1\LaravelAuthBuilder\Helpers\Response;
use io3x1\LaravelAuthBuilder\Interfaces\Auth;

class BuildAuth extends Controller implements Auth
{
    /**
     * @var string|\Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    public string $guard;
    /**
     * @var bool|\Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    public bool $otp;
    /**
     * @var string|\Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    public string $model;
    /**
     * @var string|\Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    public string $loginBy;
    /**
     * @var string|\Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    public string $loginType;
    /**
     * @var array|\Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    public array $validation;

    /**
     *
     */
    public function __construct()
    {
        $this->guard = config('laravel-auth-builder.guard');
        $this->otp = config('laravel-auth-builder.otp');
        $this->model = config('laravel-auth-builder.model');
        $this->loginBy = config('laravel-auth-builder.login_by');
        $this->loginType = config('laravel-auth-builder.login_type');
        $this->validation = config('laravel-auth-builder.validation');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(array_merge([
            "password" => "required|confirmed|min:6|max:191"
        ], $this->validation['create']));

        $data = $request->all();
        $data['password'] = bcrypt($request->get('password'));

        $user = app($this->model)->create($data);

        if ($user) {
            if($this->otp){
                $user->otp_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
                $user->save();

                SendOTP::dispatch($this->model, $user->id);

                return Response::success('An OTP Has been send to your '.$this->loginType . ' please check it');
            }

            $token = $user->createToken($this->guard)->plainTextToken;
            $user->token = $token;
            return Response::success('User registered successfully',$user);
        }

        return Response::error('User registration failed');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            $this->loginBy => 'required' . $this->loginType==='email'? '|email' : '',
            'password' => 'required'
        ]);

        $check = auth($this->guard)->attempt($request->only([$this->loginBy, "password"]));

        if($check){
            $user = auth($this->guard)->user();
            if($user->is_active && $this->otp){
                $token = $user->createToken($this->guard)->plainTextToken;
                $user->token = $token;
                return Response::success("Login Success", $user);
            }
            else if(!$user->is_active && $this->otp){
                return Response::error("Your account is not active yet");
            }
            else if(!$this->otp) {
                $token = $user->createToken($this->guard)->plainTextToken;
                $user->token = $token;
                return Response::success("Login Success", $user);
            }
        }

        return Response::success("Login Error");
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        auth($this->guard)->logout();

        $user = $this->model::find($request->user()->id);
        $user->tokens()->delete();

        return Response::success("Logout Success");
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resend(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            $this->loginBy => "required|exists:".app($this->model)->getTable().",".$this->loginBy,
        ]);

        $checkIfActive = $this->model::where($this->loginBy, $request->get($this->loginBy))->whereNotNull('otp_active_at')->first();
        if ($checkIfActive) {
            return Response::error(__('Your Account is already activated'));
        }

        $checkIfEx = $this->model::where($this->loginBy, $request->get($this->loginBy))->first();
        $checkIfEx->otp_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
        $checkIfEx->save();

        SendOTP::dispatch($this->model, $checkIfEx->id);

        return Response::success('An OTP Has been send to your '.$this->loginType . ' please check it');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        return Response::success("Profile Data Load", $user);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        $validation = $this->validation['update'];
        foreach ($validation as $key=>$item){
            if(str_contains($item, 'unique')){
                $validation[$key].=',id,'.$user->id;
            }
        }

        $request->validate($this->validation['update']);

        $getUserModel = $this->model::find($user->id);

        $getUserModel->update($request->all());

        return Response::success("Profile Data Updated", $getUserModel);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function password(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $request->validate([
            'password' => "required|confirmed|min:6|max:191",
        ]);

        $getUserModel = $this->model::find($user->id);
        $getUserModel->password = bcrypt($request->get('password'));
        $getUserModel->save();

        return Response::success("Password Updated");

    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            $this->loginBy => "required|exists:".app($this->model)->getTable().",".$this->loginBy,
            'password' => "required|confirmed|min:6|max:191",
        ]);

        $getUserModel = $this->model::where($this->loginBy, $request->get($this->loginBy))->first();
        $getUserModel->password = bcrypt($request->get('password'));
        $getUserModel->save();

        return Response::success("Password Reset");
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function otp(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            $this->loginBy => 'required|string|max:255',
            'otp_code' => 'required|string|max:6',
        ]);

        $user = app($this->model)->where($this->loginBy, $request->get($this->loginBy))->first();

        if ($user) {
            if ((!empty($user->otp_code)) && ($user->otp_code == $request->get('otp_code'))) {
                $user->otp_active_at = Carbon::now();
                $user->otp_code = null;
                $user->is_active = true;
                $user->save();

                return Response::success('your Account has been activated');
            }

            return Response::error(__('sorry this code is not valid or expired'));
        }

        return Response::error(__('user not found'), null, 404);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $this->model::where($this->loginBy, $user->{$this->loginBy})->delete();
        return Response::success('Account Has Been Deleted');
    }
}
