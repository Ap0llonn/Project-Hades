<?php

namespace App\Features\Auth\MFA\TOTP\Generate;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Ecotone\Modelling\CommandBus;
use Illuminate\Http\RedirectResponse;
use OTPHP\TOTP;

final class GenerateTotpController
{
    public function __invoke(GenerateTotpRequest $request, CommandBus $commandBus): RedirectResponse
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $totp = TOTP::create();
        $secret = $totp->getSecret();

        $commandBus->send(new GenerateTotpCommand(
            $user->id,
            $secret
        ));

        $totp->setLabel($user->email ?? 'user@vaultguardian.test');
        $totp->setIssuer('VaultGuardian');
        $provisioningUri = $totp->getProvisioningUri();

        $renderer = new ImageRenderer(
            new RendererStyle(220),
            new SvgImageBackEnd(),
        );

        $writer = new Writer($renderer);
        $qrSvg = $writer->writeString($provisioningUri);

        return redirect()->route('settings')->with('totpSetup', [
            'setupKey' => $secret,
            'provisioningUri' => $provisioningUri,
            'qrSvg' => $qrSvg,
        ]);
    }
}
