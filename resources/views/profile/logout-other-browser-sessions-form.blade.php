<x-action-section>
  <x-slot name="title">
    {{ __('Browser Sessions') }}
  </x-slot>

  <x-slot name="description">
    {{ __('Manage and log out your active sessions on other browsers and devices.') }}
  </x-slot>

  <x-slot name="content">
    <x-action-message on="loggedOut">
      {{ __('Done.') }}
    </x-action-message>

    <p class="card-text">
      {{ __('If necessary, you may log out of all of your other browser sessions across all of your devices. Some of your recent sessions are listed below; however, this list may not be exhaustive. If you feel your account has been compromised, you should also update your password.') }}
    </p>

    @if (count($this->sessions) > 0)
      <div class="mt-6">
        <!-- Other Browser Sessions -->
        @foreach ($this->sessions as $session)
          <div class="d-flex">
            <div>
              @if ($session->agent->isDesktop())
                <svg fill="none" width="32" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  viewBox="0 0 24 24" stroke="currentColor" class="text-body-secondary">
                  <path
                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                  </path>
                </svg>
              @else
                <svg xmlns="http://www.w3.org/2000/svg" width="32" viewBox="0 0 24 24" stroke-width="2"
                  stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"
                  class="text-body-secondary">
                  <path d="M0 0h24v24H0z" stroke="none"></path>
                  <rect x="7" y="4" width="10" height="16" rx="1"></rect>
                  <path d="M11 5h2M12 17v.01"></path>
                </svg>
              @endif
            </div>

            <div class="ms-2">
              <div>
                {{ $session->agent->platform() ? $session->agent->platform() : 'Unknown' }} -
                {{ $session->agent->browser() ? $session->agent->browser() : 'Unknown' }}
              </div>

              <div>
                <div class="small text-body-secondary">
                  {{ $session->ip_address }},

                  @if ($session->is_current_device)
                    <span class="text-success fw-medium">{{ __('This device') }}</span>
                  @else
                    {{ __('Last active') }} {{ $session->last_active }}
                  @endif
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif

    <div class="d-flex mt-6">
      <x-button wire:click="confirmLogout" wire:loading.attr="disabled">
        {{ __('Log Out Other Browser Sessions') }}
      </x-button>
    </div>

    <!-- Log out Other Devices Confirmation Modal -->
    <x-dialog-modal wire:model.live="confirmingLogout">
      <x-slot name="title">
        {{ __('Log Out Other Browser Sessions') }}
      </x-slot>

      <x-slot name="content">
        {{ __('Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.') }}

        <div class="mt-3" x-data="{}"
          x-on:confirming-logout-other-browser-sessions.window="setTimeout(() => $refs.password.focus(), 250)">
          <x-input type="password" placeholder="{{ __('Password') }}" x-ref="password"
            class="{{ $errors->has('password') ? 'is-invalid' : '' }}" wire:model="password"
            wire:keydown.enter="logoutOtherBrowserSessions" />

          <x-input-error for="password" class="mt-2" />
        </div>
      </x-slot>

      <x-slot name="footer">
        <x-secondary-button wire:click="$toggle('confirmingLogout')" wire:loading.attr="disabled">
          {{ __('Cancel') }}
        </x-secondary-button>

        <button class="btn btn-danger ms-1" wire:click="logoutOtherBrowserSessions" wire:loading.attr="disabled">
          {{ __('Log out Other Browser Sessions') }}
        </button>
      </x-slot>
    </x-dialog-modal>
  </x-slot>

</x-action-section>
