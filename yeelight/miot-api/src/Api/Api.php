<?php
/**
 * Created by PhpStorm.
 * User: sheldon
 * Date: 18-6-14
 * Time: 下午4:39.
 */

namespace MiotApi\Api;

use MiotApi\Contract\Instance\Instance;
use MiotApi\Exception\ApiErrorException;

/**
 * 更方便的API调用.
 *
 * Class Api
 */
class Api extends BaseApi
{
    /**
     * 一次性获取到包含了 serialNumber （原did）的设备列表.
     *
     * @return array|mixed
     */
    public function devicesList()
    {
        $devicesList = [];
        $devices = $this->devices();

        if (isset($devices['devices'])) {
            if (!empty($devices['devices'])) {
                foreach ($devices['devices'] as $device) {
                    $dids[] = $device['did'];
                    $device['serialNumber'] = null;
                    $devicesList[$device['did']] = $device;
                }

                $deviceInformations = $this->deviceInformation($dids);

                if (isset($deviceInformations['device-information'])) {
                    foreach ($deviceInformations['device-information'] as $deviceInformation) {
                        if (isset($devicesList[$deviceInformation['id']])) {
                            $devicesList[$deviceInformation['id']]['serialNumber'] = $deviceInformation['serialNumber'];
                        }
                    }
                }

                return array_values($devicesList);
            } else {
                // 没有设备的情况
                return [];
            }
        } else {
            // 获取设备出错
            return $devices;
        }
    }

    /**
     * 按照名称获取属性.
     *
     * @param $did
     * @param $type
     * @param $data | $data = ['brightness', 'on']
     *
     * @throws ApiErrorException
     * @throws \MiotApi\Exception\SpecificationErrorException
     *
     * @return array|bool|mixed
     */
    public function getPropertyGraceful($did, $type, $data)
    {
        $propertyData = [
            $did => [
                'type' => $type,
                'data' => $data,
            ],
        ];

        return $this->getPropertiesGraceful($propertyData);
    }

    /**
     * 按照名称获取多个设备属性.
     *
     * @param $data
     * $data = ['AABBCD-did' =>
     * ['type' => 'urn:miot-spec-v2:device:light:0000A001:yeelink-color1:1', data => ['brightness', 'on']]
     * ]
     *
     * @throws ApiErrorException
     * @throws \MiotApi\Exception\SpecificationErrorException
     *
     * @return array|bool|mixed
     */
    public function getPropertiesGraceful($data)
    {
        if (!empty($data)) {
            $properties = [];
            $attributes = [];
            $instances = [];
            foreach ($data as $did => $datum) {
                if (isset($datum['type'])) {
                    $instance = new Instance($datum['type']);
                    $propertiesNodes = $instance->getPropertiesNodes();
                    $instances[$did] = $propertiesNodes;

                    if (!empty($datum['data'])) {
                        foreach ($datum['data'] as $name) {
                            list($sids, $pids) = $instance->getSidPidByName($name);

                            if (!$sids || !$pids) {
                                throw new ApiErrorException('Invalid property! did:'.$did.',name: '.$name);
                            }

                            foreach ($sids as $sindex => $sid) {
                                $property = $propertiesNodes[($sid.'.'.$pids[$sindex])];

                                if (!$property->canRead()) {
                                    throw new ApiErrorException(
                                        'The property does\'t has the read access! did:'.$did.',name: '.$name
                                    );
                                }

                                $properties[] = $did.'.'.$sid.'.'.$pids[$sindex];
                            }
                        }
                    } else {
                        foreach ($propertiesNodes as $property) {
                            $name = $property->getUrn()->getName();
                            list($sids, $pids) = $instance->getSidPidByName($name);

                            if (!$sids || !$pids) {
                                throw new ApiErrorException('Invalid property! did:'.$did.',name: '.$name);
                            }

                            foreach ($sids as $sindex => $sid) {
                                $property = $propertiesNodes[($sid.'.'.$pids[$sindex])];

                                if ($property->canRead()) {
                                    $properties[] = $did.'.'.$sid.'.'.$pids[$sindex];
                                }
                            }
                        }
                    }
                } else {
                    throw new ApiErrorException('Properties data and device type required');
                }
            }

            $response = $this->properties(array_unique($properties));
            if (isset($response['properties']) && !empty($response['properties'])) {
                foreach ($response['properties'] as $res) {
                    $pidArr = explode('.', $res['pid']);
                    if (isset($res['value']) // 是否获取到了值
                        && isset($res['status']) // 是否有返回状态
                        && $res['status'] == 0 // 是否正常返回
                        && isset($pidArr[0]) // did
                        && isset($pidArr[1]) // sid
                        && isset($pidArr[2]) // pid
                        && isset($instances[$pidArr[0]][($pidArr[1].'.'.$pidArr[2])])) {
                        $attributeName = $instances[$pidArr[0]][($pidArr[1].'.'.$pidArr[2])]->getUrn()->getName();

                        if (isset($attributes[$pidArr[0]][$attributeName])) {
                            if (is_array($attributes[$pidArr[0]][$attributeName])) {
                                $attributes[$pidArr[0]][$attributeName][] = $res['value'];
                            } else {
                                $attributes[$pidArr[0]][$attributeName] = [
                                    $attributes[$pidArr[0]][$attributeName],
                                    $res['value'],
                                ];
                            }
                        } else {
                            $attributes[$pidArr[0]][$attributeName] = $res['value'];
                        }
                    }
                }
            }

            return $attributes;
        } else {
            throw new ApiErrorException('devices data required');
        }
    }

    /**
     * 按照名称设置属性.
     *
     * @param $did
     * @param $type
     * @param $data | $data = ['brightness' => 75, 'on' => true]
     *
     * @throws ApiErrorException
     * @throws \MiotApi\Exception\JsonException
     * @throws \MiotApi\Exception\SpecificationErrorException
     *
     * @return array|bool|mixed
     */
    public function setPropertyGraceful($did, $type, $data)
    {
        if (!empty($data)) {
            $propertyData = [
                $did => [
                    'type' => $type,
                    'data' => $data,
                ],
            ];

            return $this->setPropertiesGraceful($propertyData);
        } else {
            throw new ApiErrorException('Properties data required');
        }
    }

    /**
     * 按照名称设置多个设备属性.
     *
     * @param $data
     * $data = ['AABBCD-did' =>
     * ['type' => 'urn:miot-spec-v2:device:light:0000A001:yeelink-color1:1', data => ['brightness' => 75, 'on' => true]]
     * ]
     *
     * @throws ApiErrorException
     * @throws \MiotApi\Exception\JsonException
     * @throws \MiotApi\Exception\SpecificationErrorException
     *
     * @return array|bool|mixed
     */
    public function setPropertiesGraceful($data)
    {
        if (!empty($data)) {
            $properties = [];
            foreach ($data as $did => $datum) {
                if (!empty($datum['data']) && isset($datum['type'])) {
                    $instance = new Instance($datum['type']);
                    $propertiesNodes = $instance->getPropertiesNodes();

                    foreach ($datum['data'] as $name => $value) {
                        list($sids, $pids) = $instance->getSidPidByName($name);

                        if (!$sids || !$pids) {
                            throw new ApiErrorException('Invalid property! did:'.$did.',name: '.$name);
                        }

                        foreach ($sids as $sindex => $sid) {
                            $property = $propertiesNodes[($sid.'.'.$pids[$sindex])];

                            if (!is_array($value)) {
                                $tmpValue = $value;
                            } else {
                                if (!isset($value[$sindex])) {
                                    continue;
                                }
                                $tmpValue = $value[$sindex];
                            }

                            if (!$property->verify($tmpValue)) {
                                throw new ApiErrorException('Invalid property value! did:'.$did.',name: '.$name);
                            }

                            if (!$property->canWrite()) {
                                throw new ApiErrorException(
                                    'The property does\'t has the write access! did:'.$did.',name: '.$name
                                );
                            }

                            $properties[] = [
                                'pid'   => $did.'.'.$sid.'.'.$pids[$sindex],
                                'value' => $tmpValue,
                            ];
                        }
                    }
                } else {
                    throw new ApiErrorException('Properties data and device type required');
                }
            }

            return $this->setProperties([
                'properties' => $properties,
            ]);
        } else {
            throw new ApiErrorException('devices data required');
        }
    }

    /**
     * 根据 devicesList 方法获取到的设备列表信息 订阅设备属性变化.
     *
     * @param $devices
     * @param $customData
     * @param $receiverUrl
     *
     * @throws \MiotApi\Exception\SpecificationErrorException
     *
     * @return array|bool|mixed
     */
    public function subscriptByDevices($devices, $customData, $receiverUrl)
    {
        $subscriptProperties = $this->getPropertiesByDevices($devices, ['notify']);

        return $this->subscript($subscriptProperties, $customData, $receiverUrl);
    }

    /**
     * 根据 devicesList 方法获取到的设备列表信息 退订设备属性变化.
     *
     * @param $devices
     *
     * @throws \MiotApi\Exception\SpecificationErrorException
     *
     * @return array|bool|mixed
     */
    public function unSubscriptByDevices($devices)
    {
        $subscriptProperties = $this->getPropertiesByDevices($devices, ['notify']);

        return $this->unSubscript($subscriptProperties);
    }

    /**
     * 根据设备列表和 access列表 获取对于访问方式的属性.
     *
     * @param $devices
     * @param array $access | ['read'] ['read', 'notify'] ['read', 'write', 'notify']
     *
     * @throws \MiotApi\Exception\SpecificationErrorException
     *
     * @return array|bool
     */
    protected function getPropertiesByDevices($devices, $access = [])
    {
        try {
            $properties = [];
            if (!empty($devices) && !isset($devices['status'])) {
                foreach ($devices as $device) {
                    $instance = new Instance($device['type']);
                    $propertiesNodes = $instance->getPropertiesNodes();
                    if (!empty($propertiesNodes)) {
                        foreach ($propertiesNodes as $index => $property) {
                            if (in_array('read', $access)) {
                                if ($property->canRead()) {
                                    $properties[] = $device['did'].'.'.$index;
                                }
                            }
                            if (in_array('write', $access)) {
                                if ($property->canWrite()) {
                                    $properties[] = $device['did'].'.'.$index;
                                }
                            }
                            if (in_array('notify', $access)) {
                                if ($property->canNotify()) {
                                    $properties[] = $device['did'].'.'.$index;
                                }
                            }
                        }
                    }
                }
            } else {
                throw new ApiErrorException('invalid devices lists');
            }

            return $properties;
        } catch (ApiErrorException $exception) {
            throw new ApiErrorException('Could not get properties by devices:'.$exception->getMessage());

            return false;
        }
    }
}
