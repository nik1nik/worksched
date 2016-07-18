<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Shift;
use AppBundle\Entity\User;
use AppBundle\Entity\ShiftAssignment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DateTime;

class ShiftController extends Controller
{
    protected function checkToken($wtoken,$role)
    {
        $validate = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findOneBy([
                'wtoken' => $wtoken,
                'role' => $role
            ]);

        if(!$validate){
            return false;
        } else {
            return $validate->getId();
        }
    }

    /**
     * @Route("/employee/when", name="employee_when")
     * @Method("GET")oken
     * @return JsonResponse
     */
    public function whenAction(Request $request)
    {
        $wtoken = $request->headers->get('W-Token');
        $id= $this->checkToken($wtoken,'employee');
        if($id) {
            $results = $this->getDoctrine()
                ->getRepository('AppBundle:Shift')
                ->createQueryBuilder('s')
                ->select('s.startTime, s.endTime')
                ->innerJoin('AppBundle:ShiftAssignment', 'a', 'WITH', 's.id = a.shiftId')
                ->where('a.employeeId = :query')
                ->orderBy('s.startTime', 'ASC')
                ->setParameter('query', $id)
                ->getQuery()
                ->getResult();

            if (!$results) {
                $reason = [
                    'why' => 'no employee with id ' . $id . ' found'
                ];
                $data = [
                    'status' => 'fail',
                    'employee id' => $id,
                    'data' => $reason
                ];
            } else {
                foreach ($results as $key => $result) {
                    $jresults[$key]['start_time'] = $result['startTime']->format('g:ia \o\n l jS F Y');
                    $jresults[$key]['end_time'] = $result['endTime']->format('g:ia \o\n l jS F Y');
                }
                $data = [
                    'status' => 'success',
                    'employee id' => $id,
                    'data' => $jresults
                ];
            }
            return new JsonResponse($data);
        } else {
            $reason= [
                'message' => 'The user does not have sufficient permission'
            ];
            $data = [
                'status' => 'fail',
                'code' => 403,
                'employee id' => $id,
                'data' => $reason
            ];
            return new JsonResponse($data);
        }
    }

    /**
     * @Route("/employee/who/{id}", name="employee_who")
     * @Method("GET")
     * @return JsonResponse
     */
    public function whoAction($id, Request $request)
    {
        $wtoken = $request->headers->get('W-Token');
        $tokenValid= $this->checkToken($wtoken,'employee');
        if($tokenValid) {
            $results = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->createQueryBuilder('u')
                ->select('u.name')
                ->innerJoin('AppBundle:ShiftAssignment', 'a', 'WITH', 'u.id = a.employeeId')
                ->where('a.shiftId = :query')
                ->orderBy('u.name', 'ASC')
                ->setParameter('query', $id)
                ->getQuery()
                ->getResult();

            if (!$results) {
                $reason= [
                    'why' => 'there is no shift with id '.$id
                ];
                $data = [
                    'status' => 'fail',
                    'shift id' => $id,
                    'data' => $reason
                ];
            } else {
                $data = [
                    'status' => 'success',
                    'shift id' => $id,
                    'data' => $results
                ];
            }
            return new JsonResponse($data);
        } else {
            $reason= [
                'message' => 'The user does not have sufficient permission'
            ];
            $data = [
                'status' => 'fail',
                'code' => 403,
                'shift id' => $id,
                'data' => $reason
            ];
            return new JsonResponse($data);
        }
    }

    /**
     * @Route("/employee/worked", name="employee_worked")
     * @Method("GET")
     * @return JsonResponse
     */
    public function workedAction(Request $request)
    {
        $wtoken = $request->headers->get('W-Token');
        $id= $this->checkToken($wtoken,'employee');
        $start = $request->query->get('start');
        $end = $request->query->get('end');

        if($id) {
            $results = $this->getDoctrine()
                ->getRepository('AppBundle:Shift')
                ->createQueryBuilder('s')
                ->select('sum(TIMESTAMPDIFF(hour, s.startTime, s.endTime))')
                ->innerJoin('AppBundle:ShiftAssignment', 'a', 'WITH', 's.id = a.shiftId')
                ->where('a.employeeId = :query')
                ->andWhere('s.startTime >= :start')
                ->andWhere('s.endTime <= :end')
                ->setParameter('query', $id)
                ->setParameter('start', $start)
                ->setParameter('end', $end)
                ->getQuery()
                ->getSingleScalarResult();

            if (!$results) {
                $reason= [
                    'why' => 'there is no shift with for employee '.$id.' between '.$start.' and '.$end
                ];
                $data = [
                    'status' => 'fail',
                    'employee id' => $id,
                    'start' => $start,
                    'end' => $end,
                    'data' => $reason
                ];
            } else {
                $jresults[]['hours'] = $results;

                $data = [
                    'status' => 'success',
                    'employee id' => $id,
                    'start' => $start,
                    'end' => $end,
                    'data' => $jresults
                ];
            }
            return new JsonResponse($data);
        } else {
            $reason= [
                'message' => 'The user does not have sufficient permission'
            ];
            $data = [
                'status' => 'fail',
                'code' => 403,
                'employee id' => $id,
                'start' => $start,
                'end' => $end,
                'data' => $reason
            ];
            return new JsonResponse($data);
        }
    }

    /**
     * @Route("/employee/contact/{id}", name="employee_contact")
     * @Method("GET")
     * @return JsonResponse
     */
    public function contactAction($id, Request $request)
    {
        $wtoken = $request->headers->get('W-Token');
        $tokenValid= $this->checkToken($wtoken,'employee');
        if($tokenValid) {
            $results = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->createQueryBuilder('u')
                ->select('u.name, u.phone, u.email')
                ->innerJoin('AppBundle:Shift', 's', 'WITH', 'u.id = s.managerId')
                ->where('s.id = :query')
                ->orderBy('u.name', 'ASC')
                ->setParameter('query', $id)
                ->getQuery()
                ->getResult();

            if (!$results) {
                $reason= [
                    'why' => 'there is no shift with id '.$id
                ];
                $data = [
                    'status' => 'fail',
                    'shift id' => $id,
                    'data' => $reason
                ];
            } else {
                $data = [
                    'status' => 'success',
                    'shift id' => $id,
                    'data' => $results
                ];
            }
            return new JsonResponse($data);
        } else {
            $reason= [
                'message' => 'The user does not have sufficient permission'
            ];
            $data = [
                'status' => 'fail',
                'code' => 403,
                'shift id' => $id,
                'data' => $reason
            ];
            return new JsonResponse($data);
        }
    }

    /**
     * @Route("/manager/shift", name="manager_shift")
     * @Method("GET")
     * @return JsonResponse
     */
    public function shiftAction(Request $request)
    {
        $wtoken = $request->headers->get('W-Token');
        $start = $request->query->get('start');
        $end = $request->query->get('end');
        $tokenValid= $this->checkToken($wtoken,'manager');
        if($tokenValid) {
            $results = $this->getDoctrine()
                ->getRepository('AppBundle:Shift')
                ->createQueryBuilder('s')
                ->select('s.startTime, s.endTime')
                ->andWhere('s.startTime >= :start')
                ->andWhere('s.endTime <= :end')
                ->setParameter('start', $start)
                ->setParameter('end', $end)
                ->getQuery()
                ->getResult();

            if (!$results) {
                $reason= [
                    'why' => 'there are no shifts between '.$start.' and '.$end
                ];
                $data = [
                    'status' => 'fail',
                    'starting from' => $start,
                    'ending at' => $end,
                    'data' => $reason
                ];
            } else {
                foreach ($results as $key => $result) {
                    $jresults[$key]['start_time'] = $result['startTime']->format('g:ia \o\n l jS F Y');
                    $jresults[$key]['end_time'] = $result['endTime']->format('g:ia \o\n l jS F Y');
                }
                $data = [
                    'status' => 'success',
                    'starting from' => $start,
                    'ending at' => $end,
                    'data' => $jresults
                ];
            }
            return new JsonResponse($data);
        } else {
            $reason= [
                'message' => 'The user does not have sufficient permission'
            ];
            $data = [
                'status' => 'fail',
                'code' => 403,
                'starting from' => $start,
                'ending at' => $end,
                'data' => $reason
            ];
            return new JsonResponse($data);
        }
    }

    /**
     * @Route("/manager/contact/{id}", name="manager_contact")
     * @Method("GET")
     * @return JsonResponse
     */
    public function mgrcontactAction($id, Request $request)
    {
        $wtoken = $request->headers->get('W-Token');
        $tokenValid= $this->checkToken($wtoken,'manager');
        if($tokenValid) {
            $results = $this->getDoctrine()
                ->getRepository('AppBundle:User')
                ->createQueryBuilder('u')
                ->select('u.name, u.phone, u.email')
                ->innerJoin('AppBundle:ShiftAssignment', 'a', 'WITH', 'u.id = a.employeeId')
                ->where('a.shiftId = :query')
                ->orderBy('u.name', 'ASC')
                ->setParameter('query', $id)
                ->getQuery()
                ->getResult();

            if (!$results) {
                $reason= [
                    'why' => 'there is no shift with id '.$id
                ];
                $data = [
                    'status' => 'fail',
                    'shift id' => $id,
                    'data' => $reason
                ];
            } else {
                $data = [
                    'status' => 'success',
                    'shift id' => $id,
                    'data' => $results
                ];
            }
            return new JsonResponse($data);
        } else {
            $reason= [
                'message' => 'The user does not have sufficient permission'
            ];
            $data = [
                'status' => 'fail',
                'code' => 403,
                'shift id' => $id,
                'data' => $reason
            ];
            return new JsonResponse($data);
        }
    }

    /**
     * @Route("/manager/schedule", name="manager_schedule")
     * @Method("PUT")
     * @return JsonResponse
     */
    public function scheduleAction(Request $request)
    {
        $action = '';
        $date= new \DateTime("now");
        $start = new \DateTime($request->request->get('start'));
        $end = new \DateTime($request->request->get('end'));
        $managerId = $request->request->get('manager_id');
        $employeeId = $request->request->get('employee_id');
        $breakTime = $request->request->get('break_time');
        $wtoken = $request->headers->get('W-Token');
        $id= $this->checkToken($wtoken,'manager');
        if($id) {
            if($managerId==''){ //if manager Id is blank, creator Id is manager Id
                $managerId = $id;
            }

            $shift = $this->getDoctrine()
                ->getRepository('AppBundle:Shift')
                ->findOneBy([
                    'managerId' => $managerId,
                    'startTime' => $start,
                    'endTime' => $end
                ]);
            $em = $this->container->get('doctrine')->getManager();
            if(!$shift){
                $shift = new Shift();
                $shift->setStartTime($start);
                $shift->setEndTime($end);
                $shift->setManagerId($managerId);
                $shift->setBreak($breakTime);
                $shift->setCreatedAt($date);
                $shift->setUpdatedAt($date);
                $em->persist($shift);
                $em->flush();
                $action .= 'new shift added ';
            }
            $shiftId = $shift->getId();

            $shiftAssignment = $this->getDoctrine()
                ->getRepository('AppBundle:ShiftAssignment')
                ->findOneBy([
                    'shiftId' => $shiftId,
                    'employeeId' => $employeeId
                ]);
            if(!$shiftAssignment){
                $shiftAssignment = new ShiftAssignment();
                $shiftAssignment->setShiftId($shiftId);
                $shiftAssignment->setEmployeeId($employeeId);
                $shiftAssignment->setCreatedAt($date);
                $shiftAssignment->setUpdatedAt($date);
                $em->persist($shiftAssignment);
                $em->flush();
                $action .= 'new shift assignment added';
            }

            $data = [
                'status' => 'success',
                'shift id' => $shiftId,
                'employee id' => $employeeId,
                'manager id' => $managerId,
                'action' => $action
            ];
            return new JsonResponse($data);
        } else {
            $reason= [
                'message' => 'The user does not have sufficient permission'
            ];
            $data = [
                'status' => 'fail',
                'code' => 403,
                'employee id' => $employeeId,
                'data' => $reason
            ];
            return new JsonResponse($data);
        }
    }

    /**
     * @Route("/manager/change", name="manager_change")
     * @Method("PUT")
     * @return JsonResponse
     */
    public function changeAction(Request $request)
    {
        $date= new \DateTime("now");
        $start = new \DateTime($request->request->get('start'));
        $end = new \DateTime($request->request->get('end'));
        $shiftId = $request->request->get('shift_id');
        $wtoken = $request->headers->get('W-Token');
        $tokenValid= $this->checkToken($wtoken,'manager');
        if($tokenValid) {
            $shift = $this->getDoctrine()
                ->getRepository('AppBundle:Shift')
                ->findOneBy([
                    'id' => $shiftId
                ]);

            $shift->setStartTime($start);
            $shift->setEndTime($end);
            $shift->setUpdatedAt($date);
            $em = $this->container->get('doctrine')->getManager();
            $em->persist($shift);
            $em->flush();

            $data = [
                'status' => 'success',
                'shift id' => $shiftId,
                'action' => 'shift times updated'
            ];
            return new JsonResponse($data);
        } else {
            $reason= [
                'message' => 'The user does not have sufficient permission'
            ];
            $data = [
                'status' => 'fail',
                'code' => 403,
                'shift id' => $shiftId,
                'data' => $reason
            ];
            return new JsonResponse($data);
        }
    }

    /**
     * @Route("/manager/assign", name="manager_assign")
     * @Method("PUT")
     * @return JsonResponse
     */
    public function assignAction(Request $request)
    {
        $date= new \DateTime("now");
        $shiftId = $request->request->get('shift_id');
        $currentId = $request->request->get('current_employee_id');
        $newId = $request->request->get('new_employee_id');
        $action = '';
        $wtoken = $request->headers->get('W-Token');
        $tokenValid= $this->checkToken($wtoken,'manager');
        if($tokenValid) {
            if($currentId) { //employee to be replaced for the shift
                $shiftAssignment = $this->getDoctrine()
                    ->getRepository('AppBundle:ShiftAssignment')
                    ->findOneBy([
                        'shiftId' => $shiftId,
                        'employeeId' => $currentId
                    ]);
                if($shiftAssignment){ //there is an employee to be replaced
                    $shiftAssignment->setEmployeeId($newId);
                    $action = 'updated';
                } else { //employee was not found
                    $shiftAssignment = new ShiftAssignment();
                    $shiftAssignment->setShiftId($shiftId);
                    $shiftAssignment->setEmployeeId($newId);
                    $shiftAssignment->setCreatedAt($date);
                    $action = 'added';
                }
            } else { //no current employee was set, create new assignment
                $shiftAssignment = new ShiftAssignment();
                $shiftAssignment->setShiftId($shiftId);
                $shiftAssignment->setEmployeeId($newId);
                $shiftAssignment->setCreatedAt($date);
                $action = 'added';
            }
            $shiftAssignment->setUpdatedAt($date);
            $em = $this->container->get('doctrine')->getManager();
            $em->persist($shiftAssignment);
            $em->flush();

            $data = [
                'status' => 'success',
                'shift id' => $shiftId,
                'previous employee id' => $currentId,
                'new employee id' => $newId,
                'action' => $action
            ];
            return new JsonResponse($data);
        } else {
            $reason= [
                'message' => 'The user does not have sufficient permission'
            ];
            $data = [
                'status' => 'fail',
                'code' => 403,
                'shift id' => $shiftId,
                'previous employee id' => $currentId,
                'new employee id' => $newId,
                'data' => $reason
            ];
            return new JsonResponse($data);
        }
    }
}
