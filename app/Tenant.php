<?php

namespace App;

use DB;

use App\Tenant\User;
use App\Tenant\UserGroup;
use Hyn\Tenancy\Environment;
use Hyn\Tenancy\Models\Website;
use Hyn\Tenancy\Models\Hostname;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
//use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;
//use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
use Hyn\Tenancy\Repositories\HostnameRepository;
use Hyn\Tenancy\Repositories\WebsiteRepository;

/**
 * @property Website website
 * @property Hostname hostname
 */

class Tenant
{
    public function __construct(Website $website = null, Hostname $hostname = null)
    {

        $this->website = $website ?? $sub->website;
        $this->hostname = $hostname ?? $sub->websites->hostnames->first();
    }

    /**
     * Return tenant by tenant ID
     *
     * @param [integer] $ID
     * @return Tenant
     */
    public static function getByID($ID) : Tenant
    {
        $hostname = Hostname::find($ID);

        if ($hostname) {
            
            $website = $hostname->website;

            return new Tenant($website, $hostname);
        }

        return null;
    }

    /**
     * Return tenant by tenant FQDN
     *
     * @param [string] $fqdn
     * @return Tenant
     */
    public static function getByFQDN($fqdn) : Tenant
    {
        $hostname = Hostname::where('fqdn', $fqdn . '.' . env('TENANT_URL_BASE'))->first();

        if ($hostname) {
            
            $website = $hostname->website;

            return new Tenant($website, $hostname);
        }

        return null;
    }

    public function delete()
    {
        app(HostnameRepository::class)->delete($this->hostname, true);
        app(WebsiteRepository::class)->delete($this->website, true);
    }

    public static function create($fqdn, $name, $email, $password): Tenant
    {
        // Create New Website
        $website = new Website;
        app(WebsiteRepository::class)->create($website);

        // associate the website with a hostname
        $hostname = new Hostname;
        $hostname->fqdn = $fqdn . '.' . env('TENANT_URL_BASE');
        app(HostnameRepository::class)->attach($hostname, $website);

        // make hostname current
        app(Environment::class)->tenant($website);
 
        installModules();

        addMainRoles();
        addAdmin($email, $password);

        Artisan::call('db:seed',[
            '--force' => 'true'
        ]);

        //Artisan::call('passport:install');

        return new Tenant($website, $hostname);
    }

    public static function tenantExists($name)
    {
        return Hostname::where('fqdn', $name . '.' . env('TENANT_URL_BASE'))->exists();
    }
}
