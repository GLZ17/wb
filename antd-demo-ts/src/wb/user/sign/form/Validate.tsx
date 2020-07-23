export type ValidateResult = [boolean, string];

export type Validator = (value: string) => ValidateResult;

const retrieveResult = (status: boolean, message: string): ValidateResult => {
    const msg = status ? message : '';
    return [!status, msg];
};

const bindRequired = (required: boolean, errorMessage: string): Validator => {
    return (value: string): ValidateResult => {
        return retrieveResult(required && !value, errorMessage);
    };
};

const bindMinLength = (minLength: number, errorMessage: string): Validator => {
    return (value: string) => {
        return retrieveResult(value.length < minLength, errorMessage);
    };
};

const bindMaxLength = (maxLength: number, errorMessage: string): Validator => {
    return (value: string) => {
        return retrieveResult(value.length > maxLength, errorMessage);
    };
};

const bindRegexp = (regexp: RegExp, errorMessage: string): Validator => {
    return (value: string) => {
        return retrieveResult(!value.match(regexp), errorMessage);
    };
};


const bindFunctor = (functor: Validator): Validator => {
    return functor;
};

const Validate = (value: string, validators: Validator[]): ValidateResult => {
    let res: ValidateResult = [true, ''];
    validators.forEach(fn => {
        if (res[0]) {
            res = fn(value);
        }
    });
    return res;
}

const bindValidator = {
    bindRequired,
    bindMinLength,
    bindMaxLength,
    bindRegexp,
    bindFunctor
};

export {bindValidator, Validate};