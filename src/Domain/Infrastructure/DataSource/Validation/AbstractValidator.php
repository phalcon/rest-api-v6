<?php

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Phalcon\Api\Domain\Infrastructure\DataSource\Validation;

use Phalcon\Api\Domain\Infrastructure\CommandBus\CommandInterface;
use Phalcon\Api\Domain\Infrastructure\DataSource\Interface\ValidatorEnumInterface;
use Phalcon\Filter\Validation;
use Phalcon\Filter\Validation\ValidationInterface;
use Phalcon\Filter\Validation\ValidatorInterface as PhalconValidator;

abstract class AbstractValidator implements ValidatorInterface
{
    protected string $fields = '';

    public function __construct(
        private readonly ValidationInterface $validation
    ) {
    }

    /**
     * Validate a Command and return an array of errors.
     * Empty array means valid.
     *
     * @param CommandInterface $command
     *
     * @return Result
     */
    public function validate(CommandInterface $command): Result
    {
        $errors = $this->runValidations($command);
        if (true !== empty($errors)) {
            return Result::error($errors);
        }

        return Result::success();
    }

    /**
     * @param CommandInterface $command
     *
     * @return list<array<int, string>>
     */
    protected function runValidations(CommandInterface $command): array
    {
        $enum = $this->fields;
        /** @var ValidatorEnumInterface[] $elements */
        $elements = $enum::cases();

        /**
         * The composed validation object accumulates rules, so a second call
         * would add this enum's rules again and report every failure twice -
         * and because the container hands the same instance to more than one
         * validator, one validator's rules would otherwise leak into the
         * next. Starting from an empty set makes this idempotent.
         *
         * ValidationInterface exposes no way to clear rules, so this is
         * guarded on the concrete rather than widening the contract.
         */
        if ($this->validation instanceof Validation) {
            $this->validation->setValidators([]);
        }

        /** @var ValidatorEnumInterface $element */
        foreach ($elements as $element) {
            $validators = $element->validators();
            foreach ($validators as $validator) {
                /** @var PhalconValidator $validatorObject */
                $validatorObject = new $validator(
                    [
                        'allowEmpty' => $element->allowEmpty(),
                    ]
                );
                $this->validation->add($element->name, $validatorObject);
            }
        }

        $this->validation->validate($command);
        $messages = $this->validation->getMessages();

        $results = [];
        foreach ($messages as $message) {
            $results[] = [$message->getMessage()];
        }


        return $results;
    }
}
