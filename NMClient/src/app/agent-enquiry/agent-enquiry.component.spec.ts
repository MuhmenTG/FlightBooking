import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AgentEnquiryComponent } from './agent-enquiry.component';

describe('AgentEnquiryComponent', () => {
  let component: AgentEnquiryComponent;
  let fixture: ComponentFixture<AgentEnquiryComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ AgentEnquiryComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(AgentEnquiryComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
