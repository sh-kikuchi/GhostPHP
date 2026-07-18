<?php

use PHPUnit\Framework\TestCase;
use app\services\UserService;
use app\repositories\UserRepository;
use app\aura\utils\File;
use app\aura\utils\Session;
use app\aura\utils\Mail;
use app\requests\UserRequest;
use app\requests\FileRequest;

class UserServiceTest extends TestCase
{
    private UserRepository $repo;
    private File $file;
    private Mail $mail;
    private Session $session;
    private UserService $service;

    protected function setUp(): void
    {
        $this->repo = $this->createMock(UserRepository::class);
        $this->file = $this->createMock(File::class);
        $this->mail = $this->createMock(Mail::class);
        $this->session = $this->createMock(Session::class);

        $this->service = new UserService(
            $this->repo,
            $this->file,
            $this->mail,
            $this->session
        );
    }

    public function test_check_sign()
    {
        $this->repo
            ->method('checkSign')
            ->willReturn(true);

        $this->assertTrue($this->service->checkSign());
    }

    public function test_signup_success()
    {
        $request = $this->createMock(UserRequest::class);

        $request->method('input')
            ->willReturnCallback(function ($key) {
                return match ($key) {
                    'id' => null,
                    'name' => 'taro',
                    'email' => 'test@example.com',
                    'password' => 'pass',
                    default => null,
                };
            });

        $this->repo
            ->method('signup')
            ->willReturn(true);

        // checkTokenは親依存なのでここだけ回避
        $service = $this->getMockBuilder(UserService::class)
            ->setConstructorArgs([
                $this->repo,
                $this->file,
                $this->mail
            ])
            ->onlyMethods(['checkToken'])
            ->getMock();

        $service->method('checkToken')
            ->with('signup')
            ->willReturn(true);

        $this->assertTrue($service->signup($request));
    }

    public function test_signin_success()
    {
        $request = $this->createMock(UserRequest::class);

        $request->method('input')
            ->willReturnCallback(function ($key) {
                return match ($key) {
                    'id' => null,
                    'name' => 'taro',
                    'email' => 'test@example.com',
                    'password' => 'pass',
                    default => null,
                };
            });


        $this->repo
            ->method('signin')
            ->willReturn(true);

        $service = $this->getMockBuilder(UserService::class)
            ->setConstructorArgs([
                $this->repo,
                $this->file,
                $this->mail
            ])
            ->onlyMethods(['checkToken'])
            ->getMock();

        $service->method('checkToken')
            ->with('signin')
            ->willReturn(true);

        $this->assertTrue($service->signin($request));
    }

    public function test_signout_calls_repo()
    {
        $this->repo
            ->expects($this->once())
            ->method('signout');

        $this->service->signout();
    }

    public function test_upload_calls_file()
    {
        $fileRequest = $this->createMock(FileRequest::class);

        $fileRequest
            ->method('files')
            ->willReturn(['file']);

        $this->file
            ->expects($this->once())
            ->method('uploadFile')
            ->with(['file']);

        $this->service->upload($fileRequest);
    }

    public function test_mail_calls_mailer()
    {
        $data = ['to' => 'a@test.com'];

        $this->mail
            ->expects($this->once())
            ->method('sendMail')
            ->with($data);

        $this->service->mail($data);
    }
}