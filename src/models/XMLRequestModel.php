<?php
/**
 * Created by PhpStorm.
 * User: goran.erhartic
 * Date: 18/10/2017
 * Time: 12:33 PM
 */

namespace src\models;

class XMLRequestModel
{
	private $employeeId;
	private $from;
	private $to;
	private $leaveType;
	private $reasonForLeave;
	private $isHalfDay;

	/**
	 * @return mixed
	 */
	public function getEmployeeId()
	{
		return $this->employeeId;
	}

	/**
	 * @param mixed $employeeId
	 * @return XMLRequestModel
	 */
	public function setEmployeeId($employeeId)
	{
		$this->employeeId = $employeeId;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getFrom()
	{
		return $this->from;
	}

	/**
	 * @param mixed $from
	 * @return XMLRequestModel
	 */
	public function setFrom($from)
	{
		$this->from = $from;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTo()
	{
		return $this->to;
	}

	/**
	 * @param mixed $to
	 * @return XMLRequestModel
	 */
	public function setTo($to)
	{
		$this->to = $to;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getLeaveType()
	{
		return $this->leaveType;
	}

	/**
	 * @param mixed $leaveType
	 * @return XMLRequestModel
	 */
	public function setLeaveType($leaveType)
	{
		$this->leaveType = $leaveType;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getReasonForLeave()
	{
		return $this->reasonForLeave;
	}

	/**
	 * @param mixed $reasonForLeave
	 * @return XMLRequestModel
	 */
	public function setReasonForLeave($reasonForLeave)
	{
		$this->reasonForLeave = $reasonForLeave;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getIsHalfDay()
	{
		return $this->isHalfDay;
	}

	/**
	 * @param mixed $isHalfDay
	 * @return XMLRequestModel
	 */
	public function setIsHalfDay($isHalfDay)
	{
		$this->isHalfDay = $isHalfDay;

		return $this;
	}



	public function createXMLData()
	{
		$xmlString = "<Request><Record><field name='Employee_ID'>{$this->getEmployeeId()}</field><field name='From'>{$this->getFrom()}</field><field name='To'>{$this->getTo()}</field><field name='Leavetype'>{$this->getLeaveType()}</field><field name='Reasonforleave'>{$this->getReasonForLeave()}</field><days><date name='{$this->getFrom()}'>{$this->getIsHalfDay()}</date></days></Record></Request>";

		return $xmlString;
	}

//	public function xmlSerialize(Service $service)
//	{
//		$result = $service->write('Request', [
//			'Record' =>
//				[
//					[
//						'name' => 'field',
//						'attributes' => [
//							'name' =>
//								'Employee_ID',
//						],
//						'value' => $this->employeeId,
//					],
//					[
//						'name' => 'field',
//						'attributes' => [
//							'name' =>
//								'From',
//						],
//						'value' => $this->from,
//					],
//					[
//						'name' => 'field',
//						'attributes' => [
//							'name' =>
//								'To',
//						],
//						'value' => $this->to,
//					],
//					[
//						'name' => 'field',
//						'attributes' => [
//							'name' =>
//								'Leavetype',
//						],
//						'value' => $this->leaveType,
//					],
//					[
//						'name' => 'field',
//						'attributes' => [
//							'name' =>
//								'Reasonforleave',
//						],
//						'value' => $this->reasonForLeave,
//					],
//				]
//		]);
//
//		return trim(preg_replace('/\s+/', '', $result));
//	}
}