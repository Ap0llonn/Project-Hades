import zxcvbn from 'zxcvbn';

export const MIN_PASSWORD_LENGTH = 12;
export const MIN_ZXCVBN_SCORE = 3;

const ZXCVBN_LABELS = ['Very weak', 'Weak', 'Fair', 'Strong', 'Very strong'] as const;

export interface PasswordCheck {
    key: string;
    label: string;
    met: boolean;
}

export interface PasswordEntropyResult {
    score?: number;
    feedback?: {
        warning?: string;
        suggestions?: string[];
    };
}

export function getPasswordChecks(password: string | null | undefined): PasswordCheck[] {
    const candidate = password ?? '';

    return [
        {
            key: 'length',
            label: `At least ${MIN_PASSWORD_LENGTH} characters`,
            met: candidate.length >= MIN_PASSWORD_LENGTH,
        },
        {
            key: 'lowercase',
            label: 'At least one lowercase letter',
            met: /[a-z]/.test(candidate),
        },
        {
            key: 'uppercase',
            label: 'At least one uppercase letter',
            met: /[A-Z]/.test(candidate),
        },
        {
            key: 'number',
            label: 'At least one number',
            met: /\d/.test(candidate),
        },
        {
            key: 'symbol',
            label: 'At least one symbol',
            met: /[^A-Za-z0-9]/.test(candidate),
        },
    ];
}

export function getPasswordEntropy(
    password: string | null | undefined,
    userInputs: Array<string | null | undefined> = [],
): PasswordEntropyResult | null {
    if (!password) {
        return null;
    }

    return zxcvbn(password, userInputs.filter((value): value is string => Boolean(value)));
}

export function getPasswordStrengthLabel(score: number): string {
    return ZXCVBN_LABELS[score] ?? ZXCVBN_LABELS[0];
}

export function getPasswordFeedback(entropy: PasswordEntropyResult | null): string {
    if (!entropy) {
        return '';
    }

    const warning = entropy.feedback?.warning?.trim() ?? '';
    const suggestion = entropy.feedback?.suggestions?.[0]?.trim() ?? '';
    return warning || suggestion;
}

export function getPasswordMeterClass(score: number): string {
    switch (score) {
        case 0:
            return 'bg-red-500';
        case 1:
            return 'bg-orange-500';
        case 2:
            return 'bg-amber-500';
        case 3:
            return 'bg-lime-600';
        default:
            return 'bg-green-600';
    }
}

export function getPasswordValidationMessage(
    password: string | null | undefined,
    checks: PasswordCheck[],
    entropyScore: number | null,
): string {
    if (!password) {
        return 'Password is required.';
    }

    const failedRule = checks.find((check) => !check.met);
    if (failedRule) {
        return failedRule.label;
    }

    if (entropyScore === null || entropyScore < MIN_ZXCVBN_SCORE) {
        return 'Password entropy is too weak. Use a less predictable password.';
    }

    return '';
}
