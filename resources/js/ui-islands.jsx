import React from 'react';
import { createRoot } from 'react-dom/client';
import { Button, Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/react';
import { ClassNames } from '@emotion/react';

const buttonStyles = {
    alignItems: 'center',
    backgroundColor: '#cdef84',
    border: '0',
    borderRadius: 12,
    color: '#1b1c17',
    cursor: 'pointer',
    display: 'inline-flex',
    fontSize: 14,
    fontWeight: 600,
    gap: 8,
    justifyContent: 'center',
    lineHeight: 1.4,
    minHeight: 44,
    padding: '10px 14px',
    transition: 'background-color 160ms ease, color 160ms ease, transform 160ms ease',
    width: '100%',
};

function parseCredentials(node) {
    try {
        return JSON.parse(node.dataset.credentials || '[]');
    } catch (error) {
        return [];
    }
}

function fillLoginForm(credential) {
    const emailInput = document.getElementById('EmailAddress');
    const passwordInput = document.getElementById('Password');

    if (emailInput) {
        emailInput.value = credential.email;
        emailInput.dispatchEvent(new Event('input', { bubbles: true }));
    }

    if (passwordInput) {
        passwordInput.value = credential.password;
        passwordInput.dispatchEvent(new Event('input', { bubbles: true }));
    }
}

function LoginCredentialMenu({ credentials }) {
    if (!credentials.length) {
        return null;
    }

    return (
        <ClassNames>
            {({ css, cx }) => {
                const menuButton = css({
                    ...buttonStyles,
                    '&:hover': {
                        backgroundColor: '#a7d85d',
                        transform: 'translateY(-1px)',
                    },
                    '&:focus-visible': {
                        outline: '3px solid rgba(205, 239, 132, 0.45)',
                        outlineOffset: 2,
                    },
                });
                const menuItems = css({
                    backgroundColor: '#fff',
                    border: '1px solid rgba(27, 28, 23, 0.1)',
                    borderRadius: 12,
                    boxShadow: '0 18px 50px rgba(27, 28, 23, 0.14)',
                    display: 'grid',
                    gap: 6,
                    marginTop: 8,
                    padding: 8,
                    width: '100%',
                    zIndex: 30,
                });
                const menuItem = css({
                    alignItems: 'center',
                    backgroundColor: 'transparent',
                    border: '0',
                    borderRadius: 10,
                    color: '#1b1c17',
                    cursor: 'pointer',
                    display: 'flex',
                    justifyContent: 'space-between',
                    minHeight: 44,
                    padding: '9px 10px',
                    textAlign: 'left',
                    width: '100%',
                });
                const activeItem = css({
                    backgroundColor: '#f4f8ea',
                });
                const roleLabel = css({
                    display: 'block',
                    fontSize: 13,
                    fontWeight: 700,
                });
                const emailLabel = css({
                    color: '#707070',
                    display: 'block',
                    fontSize: 12,
                    fontWeight: 400,
                    marginTop: 2,
                });
                const pill = css({
                    backgroundColor: '#1b1c17',
                    borderRadius: 999,
                    color: '#fff',
                    fontSize: 11,
                    fontWeight: 700,
                    padding: '4px 8px',
                });

                return (
                    <div className={css({ marginTop: 18, position: 'relative' })}>
                        <Menu>
                            <MenuButton as={Button} className={menuButton}>
                                Use demo credentials
                                <span aria-hidden="true">v</span>
                            </MenuButton>
                            <MenuItems anchor="bottom" className={menuItems}>
                                {credentials.map((credential) => (
                                    <MenuItem key={credential.email}>
                                        {({ focus }) => (
                                            <button
                                                className={cx(menuItem, focus && activeItem)}
                                                type="button"
                                                onClick={() => fillLoginForm(credential)}
                                            >
                                                <span>
                                                    <span className={roleLabel}>{credential.label}</span>
                                                    <span className={emailLabel}>{credential.email}</span>
                                                </span>
                                                <span className={pill}>Fill</span>
                                            </button>
                                        )}
                                    </MenuItem>
                                ))}
                            </MenuItems>
                        </Menu>
                    </div>
                );
            }}
        </ClassNames>
    );
}

const islands = {
    'login-credential-menu': LoginCredentialMenu,
};

document.querySelectorAll('[data-ui-island]').forEach((node) => {
    const Component = islands[node.dataset.uiIsland];

    if (!Component) {
        return;
    }

    createRoot(node).render(<Component credentials={parseCredentials(node)} />);
});
