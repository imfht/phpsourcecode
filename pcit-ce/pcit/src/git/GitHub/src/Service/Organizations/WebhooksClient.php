<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\Organizations;

use PCIT\GPI\ServiceClientCommon;

class WebhooksClient
{
    use ServiceClientCommon;

    /**
     * List hooks.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function list(string $org_name)
    {
        return $this->curl->get($this->api_url.'/orgs/'.$org_name.'/hooks');
    }

    /**
     * Get single hook.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function get(string $org_name, int $hook_id)
    {
        return $this->curl->get($this->api_url.'/orgs/'.$org_name.'/hooks/'.$hook_id);
    }

    /**
     * Create a hook.
     *
     * 201
     *
     * @param string $content_type json or form
     *
     * @throws \Exception
     */
    public function create(
        string $org_name,
        string $hook_name,
        array $events,
        string $url,
        string $secret,
        string $content_type = 'json',
        string $insecure_ssl = '0',
        bool $active = true
    ): void {
        $data = [
            'name' => $hook_name,
            'active' => $active,
            'events' => $events,
            'config' => [
                'url' => $url,
                'content_type' => $content_type,
                'secret' => $secret,
                'insecure_ssl' => $insecure_ssl,
            ],
        ];

        $this->curl->post($this->api_url.'/orgs/'.$org_name.'/hooks', json_encode($data));
    }

    /**
     * Edit a hook.
     *
     * @throws \Exception
     */
    public function edit(
        string $org_name,
        int $hook_id,
        array $events,
        string $url,
        string $secret,
        string $content_type = 'json',
        string $insecure_ssl = '0',
        bool $active = true
    ): void {
        $data = [
            'active' => $active,
            'events' => $events,
            'config' => [
                'url' => $url,
                'content_type' => $content_type,
                'secret' => $secret,
                'insecure_ssl' => $insecure_ssl,
            ],
        ];

        $this->curl->patch($this->api_url.'/orgs/'.$org_name.'/hooks/'.$hook_id, json_encode($data));
    }

    /**
     * Ping a hook.
     *
     * 204
     *
     * @throws \Exception
     */
    public function ping(string $org_name, int $hook_id): void
    {
        $this->curl->post($this->api_url.'/orgs/'.$org_name, '/hooks/'.$hook_id.'/pings');
    }

    /**
     * Delete a hook.
     *
     * @throws \Exception
     */
    public function delete(string $org_name, string $hook_id): void
    {
        $this->curl->delete($this->api_url.'/orgs/'.$org_name.'/hooks/'.$hook_id);
    }
}
