<section class="section {{ $data['background'] ?? '' }}"@if(! empty($data['anchor'])) id="{{ $data['anchor'] }}"@endif>
  <div class="container contact-grid">
    <div class="contact-channels">
      @foreach($data['channels'] ?? [] as $channel)
        <article class="contact-channel-item reveal">
          <span class="contact-channel-icon"><i class="{{ $channel['icon'] ?? 'fa-solid fa-envelope' }}" aria-hidden="true"></i></span>
          <div class="contact-channel-info">
            <h3>@text($channel['title'] ?? '')</h3>
            <p>@text($channel['text'] ?? '')</p>
            @if(! empty($channel['link_label']))
              <a href="{{ Cms::url($channel['link_url'] ?? '#') }}"@if(! empty($channel['new_tab'])) target="_blank" rel="noopener noreferrer"@endif>@text($channel['link_label']) <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            @endif
          </div>
        </article>
      @endforeach
    </div>
    <div class="contact-card reveal">
      <h2>@text($data['form_title'] ?? 'Send us a message')</h2>
      <p>@text($data['form_text'] ?? '')</p>
      <form class="contact-form" action="{{ route('contact.submit') }}" method="post" data-contact-form>
        @csrf
        <div class="form-row">
          <div class="form-group"><label for="contact-name">Full name *</label><input type="text" id="contact-name" name="name" autocomplete="name" required></div>
          <div class="form-group"><label for="contact-email">Email address *</label><input type="email" id="contact-email" name="email" autocomplete="email" required></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label for="contact-phone">Phone / WhatsApp</label><input type="tel" id="contact-phone" name="phone" autocomplete="tel"></div>
          <div class="form-group">
            <label for="contact-department">Department *</label>
            <select id="contact-department" name="department" required>
              @foreach($data['departments'] ?? [] as $department)
                <option>{{ $department }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group"><label for="contact-service">Domain or service ID (optional)</label><input type="text" id="contact-service" name="service"></div>
        <div class="form-group"><label for="contact-message">Message *</label><textarea id="contact-message" name="message" rows="5" required></textarea></div>
        <button type="submit" class="btn btn-primary">@text($data['button_label'] ?? 'Send Message') <i class="fa-solid fa-paper-plane" aria-hidden="true"></i></button>
        <p class="form-status" data-form-status role="status" @if(! session('contact_success')) hidden @endif>{{ session('contact_success') }}</p>
      </form>
    </div>
  </div>
</section>
