<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateSAMLCertificatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'saml:certificates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate IdP and SP certificates and private keys';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        [$key, $cert] = self::certificate([
            'countryName' => 'GB',
            'stateOrProvinceName' => 'Tyne and Wear',
            'localityName' => 'Newcastle upon Tyne',
            'organizationName' => 'iamproperty',
        ]);
        self::export('sp', $key, $cert);

        [$key, $cert] = self::certificate([
            'countryName' => 'GB',
            'stateOrProvinceName' => 'London',
            'localityName' => 'London',
            'organizationName' => 'The Guild of Property Professionals',
        ]);
        self::export('idp', $key, $cert);

        return 0;
    }

    private static function certificate(array $dn): array
    {
        $config = [
            'private_key_bits' => 4096,
        ];

        $key = openssl_pkey_new($config);
        $csr = openssl_csr_new($dn, $key, $config);
        $cert = openssl_csr_sign($csr, null, $key, 3650, $config);

        return [$key, $cert];
    }

    private static function export(string $name, $key, $cert): void
    {
        openssl_pkey_export_to_file($key, storage_path("app/saml/credentials/$name.pem"));
        openssl_x509_export_to_file($cert, storage_path("app/saml/credentials/$name.crt"));
    }
}
