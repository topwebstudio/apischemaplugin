<?php

namespace App\Controller;

use App\Entity\SchemaAuthor;
use App\Entity\SchemaBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SchemaBuilderApiController extends Controller {

    public function browseAction(Request $request) {
        $response = ['status' => 'error', 'info' => []];
        $requestParameters = $request->getMethod() === 'POST' ? unserialize($request->get('data')) : [];

        $defaults = [
            'search' => null,
            'featured' => null,
            'page' => 1,
            'numItemsPerPage' => 5
        ];

        if (is_array($requestParameters)) {
            $parameters = array_merge($defaults, $requestParameters);
        } else {
            $parameters = $defaults;
        }

        $items = $this->getEntityManager()->getRepository('App:SchemaBuilder')->fetch($parameters);
        $paginated =  $this->get('knp_paginator')->paginate($items, $parameters['page'], $parameters['numItemsPerPage']);

        $response['itemsData'] = [];
        foreach ($paginated->getItems() as $item) {
            $i = [];

            $i['title'] = $item->getTitle();
            $i['description'] = $item->getContent();
            $i['featured'] = $item->getFeatured();
            $i['pending'] = $item->getPublished() == 1 ? false : true;
            $i['schema'] = $item->getJsonSchemasArray();
            $i['date'] = $item->getDate()->format('Y-m-d');
            $i['tags'] = $item->getTags();
            $i['author_name'] = $item->getAuthor()->getName();
            $i['author_id'] = $item->getAuthor()->getId();
            $i['plugin_version_required'] = $item->getPluginVersion() ? $item->getPluginVersion()->getVersion() : false;


            array_push($response['itemsData'], $i);
        }

        if (isset($parameters['filterByAuthor']) && $parameters['filterByAuthor']) {
            $author = $this->getEntityManager()->getRepository('App:SchemaAuthor')->find($parameters['filterByAuthor']);

            if ($author) {
                $parameters['filterAuthorName'] = $author->getName();
                $parameters['filterAuthorContent'] = $author->getContent();
            }
        }


        $response['parameters'] = $parameters;
        $response['status'] = 'success';
        $response['pagination'] = $paginated->getPaginationData();
        return $this->response($response);
    }

    public function submitAction(Request $request) {
        $em = $this->getEntityManager();

        $response = ['status' => 'error', 'info' => []];
        $parameters = $request->getMethod() === 'POST' ? unserialize($request->get('data')) : [];

        $schemaBuilder = new SchemaBuilder();
        $schemaBuilder->setTitle(strip_tags($parameters['title']));
        $schemaBuilder->setContent(strip_tags($parameters['description']));
        $schemaBuilder->setTags(strip_tags($parameters['tags']));

        // get user by email and password.
        $author = $this->findAuthorByEmailAndUid($parameters['email'], $parameters['uid']);
        if (!$author) {
            $response['info'] = 'User account error.';
            return $this->response($response);
        }

        foreach ($parameters['schema'] as $_schema) {
            $jsonSchema = new \App\Entity\JsonSchema();
            $jsonSchema->setSchemaArray($_schema);
            $em->persist($jsonSchema);
            $schemaBuilder->addJsonSchema($jsonSchema);
        }

        $author->addSchemaBuilder($schemaBuilder);

        $em->persist($author);
        $em->persist($schemaBuilder);



        $em->flush();

        $response['info'] = 'Success';
        $response['status'] = 'success';

        return $this->response($response);
    }

    public function registerAction(Request $request, ValidatorInterface $validator) {
        $response = ['status' => 'error', 'info' => ['Failed to register']];
        $parameters = $request->getMethod() === 'POST' ? $request->get('data') : [];
        $em = $this->getEntityManager();
        if ($parameters['email'] && $parameters['password'] && $parameters['name']) {
            $error = false;
            $userByName = $em->getRepository('App:SchemaAuthor')->findOneBy(
                    [
                        'name' => $parameters['name']
                    ]
            );

            $userByEmail = $em->getRepository('App:SchemaAuthor')->findOneBy(
                    [
                        'email' => $parameters['email']
                    ]
            );

            if ($userByName) {
                $error = true;
                $response['info'] = 'This name is already used';
            }

            if ($userByEmail) {
                $error = true;
                $response['info'] = 'This email is already used';
            }

            if (!$error) {

                $author = new SchemaAuthor();
                $author->setUid(substr(md5(time()), 0, 12));
                $author->setName($parameters['name']);
                $author->setEmail($parameters['email']);
                $author->setPassword($parameters['password']);

                $em->persist($author);


                $errors = $validator->validate($author);
                if (count($errors) > 0) {
                    $outputErrors = '';

                    foreach ($errors as $error) {
                        $outputErrors .= $error->getMessage();
                    }

                    $response['info'] = $outputErrors;
                    return $this->response($response);
                }

                $em->flush();


                $response['uid'] = $author->getUid();
                $response['name'] = $author->getName();
                $response['email'] = $author->getEmail();


                $response['status'] = 'success';
                $response['info'] = 'Successfully registered';
            }
        }

        return $this->response($response);
    }

    public function loginAction(Request $request) {
        $response = ['status' => 'error', 'info' => ['Failed to login']];
        $parameters = $request->getMethod() === 'POST' ? $request->get('data') : [];
        $em = $this->getEntityManager();
        if ($parameters['email'] && $parameters['password']) {
            $user = $em->getRepository('App:SchemaAuthor')->findOneBy(
                    [
                        'email' => $parameters['email'],
                        'password' => $parameters['password'],
                    ]
            );

            if ($user) {
                $response['status'] = 'success';
                $response['uid'] = $user->getUid();
                $response['name'] = $user->getName();
                $response['email'] = $user->getEmail();
                $response['content'] = $user->getContent();

                $response['info'] = 'Successfully logged in';
            }
        }

        return $this->response($response);
    }

    public function saveProfileAction(Request $request) {
        $response = ['status' => 'error', 'info' => ['Failed to login']];
        $parameters = $request->getMethod() === 'POST' ? $request->get('data') : [];
        $em = $this->getEntityManager();


        if ($parameters['uid']) {
            $user = $em->getRepository('App:SchemaAuthor')->findOneBy(
                    [
                        'uid' => $parameters['uid'],
                    ]
            );

            $userByNameEmailAndDifferentUid = null;

            if ($parameters['name'] && $parameters['email']) {

                $userByNameEmailAndDifferentUid = $em->getRepository('App:SchemaAuthor')->getUserByNameEmailAndDifferentUid(
                        $parameters['uid'], $parameters['name'], $parameters['email']
                );

                if ($userByNameEmailAndDifferentUid) {
                    $response['info'] = 'Email or name already exists';
                } else if ($user) {

                    $user->setName($parameters['name']);
                    $user->setEmail($parameters['email']);
                    $user->setContent($parameters['content']);

                    if (isset($parameters['password']) && $parameters['password']) {
                        $user->setPassword($parameters['password']);
                    }

                    $em->persist($user);
                    $em->flush();

                    $response['status'] = 'success';

                    $response['name'] = $user->getName();
                    $response['email'] = $user->getEmail();
                    $response['content'] = $user->getContent();

                    $response['info'] = 'Successfully updated your profile';
                }
            }
        }

        return $this->response($response);
    }

    private function findAuthorByEmailAndUid($email, $uid) {
        $em = $this->getEntityManager();
        return $em->getRepository('App:SchemaAuthor')->findOneBy(
                        [
                            'email' => $email,
                            'uid' => $uid
                        ]
        );
    }

}
