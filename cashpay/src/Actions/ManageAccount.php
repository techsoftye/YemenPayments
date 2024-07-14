<?php

namespace  Techsoft\Cashpay\Actions;

use  Techsoft\Cashpay\Resources\Resource;

trait ManageAccount
{

    /**
     * Check a payment instance.
     *
     * @param  string  $newPassword
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function resetPassword($newPassword)
    {
        $response = $this->post('Operation/ChangePass', [
            'NewPass' => $newPassword,
        ]);

        return new Resource($response);
    }
}
