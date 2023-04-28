import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SearchHotelsComponent } from './search-hotels.component';

describe('SearchHotelsComponent', () => {
  let component: SearchHotelsComponent;
  let fixture: ComponentFixture<SearchHotelsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ SearchHotelsComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(SearchHotelsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
